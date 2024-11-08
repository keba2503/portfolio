<?php

namespace Hiperdino\Dinitos\Model\History\MovementTypes;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Data\HistoryFactory;
use Hiperdino\Dinitos\Model\Data\Package as PackageModel;
use Hiperdino\Dinitos\Model\Package\Manager;
use Hiperdino\Dinitos\Model\Repository\HistoryRepository;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\Package\GetRedeemable;
use Magento\Quote\Model\Quote;

abstract class Movement
{
    const ACCUMULATION_MOVEMENT = 0;
    const REDEMPTION_MOVEMENT = 1;
    const REDEEMED_VALUE = 1;

    public function __construct(
        protected Config $configHelper,
        protected HistoryFactory $historyFactory,
        protected HistoryRepository $historyRepository,
        protected Logger $logger,
        protected GetDinitos $customerDinitos,
        protected Manager $packageService,
        protected GetRedeemable $customerReedemablePackages,
    ) {
    }

    abstract public function handleMovement(?Quote $quote = null, $dinitos = null, ?PackageModel $package = null);

    abstract public function handleAdminMovement($data);
    abstract public function revertMovement(mixed $customerId, mixed $incrementId, mixed $movementType, string $concept, mixed $movementData, mixed $newBalance);
    public function createPackage($dinitosQty, $incrementId, $customerId): bool|PackageModel
    {
        try {
            return $this->packageService->createPackage($dinitosQty, $incrementId, $customerId);
        } catch (Exception $e) {
            $this->movementLog(__("Error en el movimiento al crear el paquete. \n {$e->getMessage()}"));

            return false;
        }
    }
    public function movementLog($message): void
    {
        $this->logger->logDinitosMovement($message);
    }
}