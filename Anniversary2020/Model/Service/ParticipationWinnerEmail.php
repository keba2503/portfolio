<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Singular\QRManager\Helper\Config as ConfigQr;
use Singular\QRManager\Helper\QR as QRManager;

class ParticipationWinnerEmail
{
    protected TransportBuilder $transportBuilder;
    protected Logger $logger;
    protected Config $helperConfig;
    protected StoreManagerInterface $storeManager;
    protected UrlInterface $url;
    protected QRManager $QR;
    protected ConfigQr $configQr;

    public function __construct(
        TransportBuilder $transportBuilder,
        Config $helperConfig,
        StoreManagerInterface $storeManager,
        Logger $logger,
        UrlInterface $url,
        QRManager $QR,
        ConfigQr $configQr
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->helperConfig = $helperConfig;
        $this->storeManager = $storeManager;
        $this->url = $url;
        $this->QR = $QR;
        $this->configQr = $configQr;
    }

    public function sendEmail($customer, $couponCode, $couponName, $couponEndDate, $filename)
    {
        $imageQr = $this->getUrlQr($filename);

        try {
            if ($this->helperConfig->getEmailEnabled()) {
                $senderIdentity = $this->helperConfig->getEmailIdentity();
                $templateId = $this->helperConfig->getEmailTemplate();

                $sendTo = $customer->getEmail();
                $this->log(__("Se comienza proceso de envio de email a cliente web al : " . $sendTo));

                $templateParams = [
                    'barcode_prize' => $couponCode,
                    'prize' => $couponName,
                    'date_until' => $couponEndDate,
                    'image_qr' => $imageQr
                ];

                $transportBuilder = $this->transportBuilder->setTemplateIdentifier($templateId);

                $transportBuilder->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        "store" => $this->storeManager->getStore()->getId()
                    ]
                );

                $transportBuilder->setTemplateVars($templateParams);
                $transportBuilder->setFrom($senderIdentity);
                $transportBuilder->addTo($sendTo);
                $transport = $transportBuilder->getTransport();
                $transport->sendMessage();

                return true;
            } else {
                $this->log("No está habilitado el envio de email de cupón por participacion premiada a cliente web");
            }
        } catch (Exception $e) {
            $this->log("No se ha podido enviar el email de cupón por participacion premiada a cliente web. Error: " . $e->getMessage());
        }

        return false;
    }

    protected function log($message): void
    {
        $this->logger->logParticipationScratch($message);
    }

    /**
     * @param $filename
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUrlQr($filename): string
    {
        $baseUrl = $this->helperConfig->getMediaUrl();
        $typePath = $this->configQr->getQRRelativePath(ConfigQr::ANNIVERSARY_COUPON);
        $generalPath = $this->configQr->getQRPath();
        $relativePath = ltrim(str_replace('pub/media/', '', $generalPath), '/');

        return $baseUrl . $relativePath . $typePath . '/' . $filename;
    }
}
