<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Exception;
use Hiperdino\Anniversary2020\Helper\Logger;
use Singular\QRManager\Helper\Config;
use Singular\QRManager\Helper\QR as QRHelper;

class Qr
{
    protected QRHelper $qrManager;
    protected Logger $logger;

    public function __construct(
        QRHelper $qrManager,
        Logger $logger
    ) {
        $this->qrManager = $qrManager;
        $this->logger = $logger;
    }

    /**
     * @param $couponcode
     * @return bool|string
     */
    public function generateQR($couponcode)
    {
        try {
            $this->logger->logParticipationScratch(__('Se comienza proceso de creación de codigo QR del cupón  : ' . $couponcode));

            $QRFilename = $this->qrManager->generateQR($couponcode, $couponcode, Config::ANNIVERSARY_COUPON, true);
        } catch (Exception $e) {
            $this->logger->logParticipationScratch($e->getMessage());
            return false;
        }

        return $QRFilename;
    }
}
