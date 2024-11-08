<?php

namespace Hiperdino\Dinitos\Model\Services\Orders;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\SetDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetMovementsCustomer;
use Hiperdino\Dinitos\Model\Services\History\GetTypeMovements;
use Magento\Framework\Exception\CouldNotSaveException;

class AfterOrderCancel
{
    const VALUE_ACCUMULATED = 0;
    const VALUE_REDEEMED = 1;
    const VALUE_REFUND = 3;
    const VALUE_DEDUCTION = 4;
    const VALUE_CANCELLED_TEXT = 'Pedido cancelado:';

    public function __construct(
        protected GetDinitos $customerDinitos,
        protected Logger $logger,
        protected SetDinitos $setDinitosService,
        protected GetMovementsCustomer $historyByCustomer,
        protected PackageRepository $packageRepository,
        protected GetTypeMovements $getTypeMovement,

    ) {
    }

    /**
     *
     * @throws Exception
     */
    public function makeMovements($order): void
    {
        $customerId = $order->getCustomerId() ;
        $incrementId = $order->getIncrementId();

        try {
            $movements = $this->historyByCustomer->callHistoryByIncrementId($customerId, $incrementId);
            $this->handleMovement($movements);

        } catch (Exception $e) {
            $this->logger->logDinitosService($e);
        }
    }

    /**
     * @throws CouldNotSaveException
     */
    private function handleMovement($movements): void
    {
        if (empty($movements)) {
            return;
        }

        $concept = self::VALUE_CANCELLED_TEXT;
        $newBalance = 0;

        foreach ($movements as $movement) {
            $movementType = $movement['transaction_type'];
            $customerId = $movement['customer_id'];
            $incrementId = $movement['increment_id'];
            $movementData = $movement;
            $historyPackage = $movement['package_history'];
            $historyPackage && $this->handlePackage($historyPackage);

            $balance = $this->customerDinitos->getCustomerDinitosTotal($customerId);

            [$newBalance, $movementType] = $this->updateBalanceAndMovementType($movementType, $balance, $movementData['dinitos_quantity'], $newBalance, $customerId);

            $movementClass = $this->getTypeMovement->getMovement($movementType);
            $movementClass->revertMovement($customerId, $incrementId, $movementType, $concept, $movementData, $newBalance);
        }
    }

    /**
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function handlePackage($historyPackage): bool
    {
        $historyPackage = json_decode($historyPackage, true);

        if (!is_array($historyPackage)) {
            return false;
        }

        foreach ($historyPackage as $packages) {
            $packageId = $packages['id'];
            $quantity = $packages['quantity'];
            $package = $this->packageRepository->getById($packageId);
            $beforeQty = $package->getAvailableDinitos();
            $newQty = $beforeQty + $quantity;
            $package->setAvailableDinitos($newQty);
            $package->setRedeemed(0);

            $this->packageRepository->save($package);

        }

        return true;
    }

    /**
     * @param mixed $movementType
     * @param mixed $balance
     * @param $dinitos_quantity
     * @param mixed $newBalance
     * @param mixed $customerId
     * @return array
     */
    private function updateBalanceAndMovementType(mixed $movementType, mixed $balance, $dinitos_quantity, mixed $newBalance, mixed $customerId): array
    {
        if ($movementType == self::VALUE_ACCUMULATED) {
            $newBalance = $balance - $dinitos_quantity;
            $movementType = self::VALUE_DEDUCTION;
            $this->setDinitosService->setCustomerDinitos($customerId, $newBalance);
        } elseif ($movementType == self::VALUE_REDEEMED) {
            $newBalance = $balance + $dinitos_quantity;
            $movementType = self::VALUE_REFUND;
            $this->setDinitosService->setCustomerDinitos($customerId, $newBalance);
        }

        return array($newBalance, $movementType);
    }
}