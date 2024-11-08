<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Hiperdino\BlackFriday\Model\StorepassTimeslotRepository;
use Singular\Tiendas\Model\TiendasRepository;
use Zend_Mime;
use Zend_Mime_Part;

class StorePassEmail extends AbstractHelper
{

    protected $_transportBuilder;
    protected $_storeManager;
    protected $_inlineTranslation;
    protected $_customerRepository;
    protected $_timeslotRepository;
    protected $_tiendasRepository;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        CustomerRepositoryInterface $customerRepository,
        StorepassTimeslotRepository $storepassTimeslotRepository,
        TiendasRepository $tiendasRepository
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $state;
        $this->_customerRepository = $customerRepository;
        $this->_timeslotRepository = $storepassTimeslotRepository;
        $this->_tiendasRepository = $tiendasRepository;
        parent::__construct($context);
    }

    /**
     * @param \Hiperdino\BlackFriday\Model\StorepassBooking $booking
     */
    public function sendBookingEmail($booking)
    {
        try {

            $customer = $this->_customerRepository->getById($booking->getData('customer_id'));
            /** @var \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot */
            $timeslot = $this->_timeslotRepository->getById($booking->getData('timeslot_id'));
            /** @var \Singular\Tiendas\Model\Tiendas $tienda */
            $tienda = $this->_tiendasRepository->getById($timeslot->getData('parent_store'));

            $templateId = 'blackfriday_storepass_booking';
            $fromEmail = (string) $this->scopeConfig->getValue('trans_email/ident_general/email');
            $fromName = (string) $this->scopeConfig->getValue('trans_email/ident_general/name');
            $toEmail = $customer->getEmail();

            $store = $this->_storeManager->getStore();
            $storeId = $store->getId();

            $templateVars = [
                'tienda_name' => $tienda->getData('name'),
                'booked_for' => date('d/m/Y', strtotime($booking->getData('booked_for') ?: "")),
                'start_end_time' => "{$timeslot->getData('start_time')} - {$timeslot->getData('end_time')}",
                'customer_name' => "{$customer->getFirstname()} {$customer->getLastname()}",
                'qr_image_cid' => $booking->getData('qr')
            ];

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->_inlineTranslation->suspend();

            //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $attachment = $transport->getMessage()->createAttachment(
                $this->_getQrDataStream($booking),
                'image/png',
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $booking->getData('qr')
            );
            $attachment->id = $booking->getData('qr');
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {
            //TODO log error
        }
    }

    /**
     * @param \Hiperdino\BlackFriday\Model\StorepassBooking $booking
     * @return string
     */
    protected function _getQrDataStream($booking)
    {
        $qrFilepath = $booking->getQrImagePath();
        $pubMediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']);
        $qrImageUrl = $pubMediaUrl . $qrFilepath;
        $qrAuthToken = (string) $this->scopeConfig->getValue('singular_qr/general/cdn_token');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $qrImageUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "hdauth: {$qrAuthToken}"
            ),
        ));
        $response = curl_exec($curl);
        return $response;
    }
}
