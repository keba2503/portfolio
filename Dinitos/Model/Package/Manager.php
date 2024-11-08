<?php

namespace Hiperdino\Dinitos\Model\Package;

use DateInterval;
use DateTime;
use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Data\Package as PackageModel;
use Hiperdino\Dinitos\Model\Data\PackageFactory;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;

class Manager
{
    const NOT_REDEEMED_VALUE = 0;
    const NOT_EXPIRED_VALUE = 0;

    public function __construct(
        protected PackageFactory $packageFactory,
        protected PackageRepository $packageRepository,
        protected Config $configHelper,
        protected Logger $logger
    ) {
    }

    public function createPackage($dinitosQty, $customerId, $incrementId = null): bool|PackageModel
    {
        try {
            $package = $this->packageFactory->create();
            $package->setCustomerId($customerId);
            $package->setIncrementId($incrementId);
            $package->setDinitosQuantity($dinitosQty);
            $package->setAvailableDinitos($dinitosQty);
            $package->setRedeemed(self::NOT_REDEEMED_VALUE);
            $package->setExpired(self::NOT_EXPIRED_VALUE);
            $package->setExpirationDate($this->getExpirationDateFromToday());
            $this->packageRepository->save($package);

            return $package;
        } catch (Exception $e) {
            $this->logger->logPackages("Error creando el paquete: \n {$e->getMessage()}");

            return false;
        }
    }

    public function updatePackage($package, $dinitosQty, $redeemed = null, $incrementId = null)
    {
        try {
            $package->setAvailableDinitos($dinitosQty);
            $package->setRedeemed($redeemed ?: self::NOT_REDEEMED_VALUE);
            $this->packageRepository->save($package);

            return $package;
        } catch (Exception $e) {
            $this->logger->logPackages("Error actualizando el paquete: \n {$e->getMessage()}");

            return false;
        }
    }

    public function getExpirationDateFromToday(): DateTime
    {
        $expirationConfig = $this->configHelper->getDinitosDaysExpiration();
        $date = new DateTime('now');
        $date->add(new DateInterval("P{$expirationConfig}D"));

        return $date;
    }
}