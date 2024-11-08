<?php

namespace Hiperdino\Dinitos\Model\Services\Package;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Hiperdino\Dinitos\Model\ResourceModel\Package\CollectionFactory as PackageCollectionFactory;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\SetDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetTypeMovements;
use Singular\FlagManager\Model\Services\FlagManager;

class Expired
{
    const EXPIRED_VALUE = 1;
    const EXPIRED_TYPE = 2;
    const EXPIRED_FLAG = 'expired_process.flag';

    public function __construct(
        protected PackageCollectionFactory $packageCollectionFactory,
        protected PackageRepository $packageRepository,
        protected Config $configHelper,
        protected Logger $logger,
        protected GetDinitos $customerDinitos,
        protected FlagManager $flagManager,
        protected GetTypeMovements $getTypeMovement,
        protected SetDinitos $setCustomerDinitosService
    ) {
    }

    public function getExpiredPackages(): array
    {
        $packages = [];
        try {
            $currentDate = date('Y-m-d H:i:s');
            $packages = $this->packageCollectionFactory->create()
                ->addExpiredFilter()
                ->addFieldToFilter('expiration_date', ['lteq' => $currentDate])
                ->getItems();
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error al recuperar los paquetes que pueden expirar: \n {$e->getMessage()}"));
        }

        return $packages;
    }

    public function processPackageExpiration(): void
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledWeb()) {
            return;
        }

        if (!$this->flagManager->generateFlag(self::EXPIRED_FLAG)) {
            $this->logger->logPackages(__("Se comienza el proceso de caducidad de los paquetes."));

            return;
        }

        try {
            $expiredPackages = $this->getExpiredPackages();
            $this->markPackagesAsExpired($expiredPackages);

            foreach ($expiredPackages as $package) {
                $movementClass = $this->getTypeMovement->getMovement(self::EXPIRED_TYPE);
                $movementClass->handleMovement(null, null, $package);
                $this->updateCustomerDinitos($package);
            }
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error en el proceso de caducidad: \n {$e->getMessage()}"));
        }

        $this->flagManager->removeFlag(self::EXPIRED_FLAG);
    }

    public function markPackagesAsExpired(array $packages): void
    {
        try {
            foreach ($packages as $package) {
                $packageId = $package->getId();
                $currentPackage = $this->packageRepository->getById($packageId);
                $currentPackage->setExpired(self::EXPIRED_VALUE);
                $this->packageRepository->save($currentPackage);
            }
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error al marcar los paquetes como expirados: \n {$e->getMessage()}"));
        }
    }

    /**
     * @param mixed $package
     * @return void
     */
    public function updateCustomerDinitos(mixed $package): void
    {
        try {
            $balance = $this->customerDinitos->getCustomerDinitosTotal($package->getCustomerId()) - $package->getAvailableDinitos();
            $this->setCustomerDinitosService->setCustomerDinitos($package->getCustomerId(), $balance);
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error al actualizar los dinitos del cliente: \n {$e->getMessage()}"));
        }
    }
}
