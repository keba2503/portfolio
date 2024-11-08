<?php

namespace Hiperdino\Dinitos\Model\History\MovementTypes;

use Exception;
use Hiperdino\Dinitos\Model\Data\Package as PackageModel;
use Magento\Quote\Model\Quote;

class AccumulationMovement extends Movement
{
    public function handleMovement(?Quote $quote = null, $dinitos = null, ?PackageModel $package = null)
    {
        try {
            $concept = str_replace('[increment_id]', $quote->getReservedOrderId(), $this->configHelper->getTextConceptAccumulation());
            $concept = str_replace('[increment_id]', '', $concept);

            $balance = $this->customerDinitos->getCustomerDinitosTotal($quote->getCustomerId()) + $dinitos;
            $package = $this->createPackage($dinitos, $quote->getCustomerId(), $quote->getReservedOrderId());
            $accumulationMovement = $this->historyFactory->create();
            $accumulationMovement->setCustomerId($quote->getCustomerId());
            $accumulationMovement->setConcept($concept);
            $accumulationMovement->setIncrementId($quote->getReservedOrderId());
            $accumulationMovement->setDinitosQuantity($dinitos);
            $accumulationMovement->setTransactionType(self::ACCUMULATION_MOVEMENT);
            $accumulationMovement->setPackageId($package->getId());
            $accumulationMovement->setDinitosBalance($balance);
            $this->historyRepository->save($accumulationMovement);
        } catch (Exception $e) {
            $this->movementLog(__("Error creando el movimiento de obtención: \n {$e->getMessage()}"));

            return false;
        }

        return true;
    }

    public function handleAdminMovement($data): bool
    {
        try {
            $dinitosQty = $data['dinitos_quantity'];
            $customerId = $data['customer_id'];
            $incrementId = $data['increment_id'];
            $concept = $data['concept'];
            $balance = $this->customerDinitos->getCustomerDinitosTotal($customerId) + $dinitosQty;
            $package = $this->createPackage($dinitosQty, $customerId, $incrementId);
            $accumulationMovement = $this->historyFactory->create();
            $accumulationMovement->setCustomerId($customerId);
            $accumulationMovement->setConcept($concept);
            $accumulationMovement->setIncrementId($incrementId);
            $accumulationMovement->setDinitosQuantity($dinitosQty);
            $accumulationMovement->setTransactionType(self::ACCUMULATION_MOVEMENT);
            $accumulationMovement->setPackageId($package->getId());
            $accumulationMovement->setDinitosBalance($balance);
            $this->historyRepository->save($accumulationMovement);
        } catch (Exception $e) {
            $this->movementLog(__("Error creando el movimiento de obtención desde el admin: \n {$e->getMessage()}"));

            return false;
        }

        return true;
    }

    public function revertMovement(mixed $customerId, mixed $incrementId, mixed $movementType, string $concept, mixed $movementData, mixed $newBalance): void
    {
        $movementObject = $this->historyFactory->create();
        $movementObject->setCustomerId($customerId);
        $movementObject->setIncrementId($incrementId);
        $movementObject->setTransactionType($movementType);
        $movementObject->setConcept("$concept {$incrementId}");
        $movementObject->setDinitosQuantity($movementData['dinitos_quantity']);
        $movementObject->setPackageId($movementData['package_id']);
        $movementObject->setDinitosBalance($newBalance);

        $this->historyRepository->save($movementObject);
    }
}