<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;

class Qr extends Action
{

    protected $_pageFactory;
    protected $_customerSession;
    protected $_bfStorePassHelper;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_bfStorePassHelper = $bfStorePassHelper;
        return parent::__construct($context);
    }

    public function execute()
    {
        $unauthResponseCode  = 403;

        $isActive = $this->_bfStorePassHelper->isActive();

        if(!$isActive || !$this->_customerSession->isLoggedIn()) {
            http_response_code($unauthResponseCode);
            die(0);
        }

        $customerId = $this->_customerSession->getId();

        if(! $this->_bfStorePassHelper->customerAlreadyHasBooking($customerId)) {
            http_response_code($unauthResponseCode);
            die(0);
        }

        $currentBooking = $this->_bfStorePassHelper->getCurrentBookingInfo($customerId);
        if(! $currentBooking) {
            http_response_code($unauthResponseCode);
            die(0);
        }

        /** @var \Hiperdino\BlackFriday\Model\StorepassBooking $booking */
        $booking = $currentBooking['booking'];
        if($booking->getData('customer_id') != $customerId) {
            http_response_code($unauthResponseCode);
            die(0);
        }
        $qrFilepath = $booking->getQrImagePath();
        $pubMediaUrl = $this->_url->getBaseUrl(['_type' => 'media']);
        $qrImageUrl = $pubMediaUrl . $qrFilepath;

        $qrAuthToken = $this->_bfStorePassHelper->getQrAuthToken();

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

        http_response_code(200);
        header("Content-Type: image/png");
        header("Content-Length: " . sizeof($response));
        echo $response;

        die(0);
    }
}
