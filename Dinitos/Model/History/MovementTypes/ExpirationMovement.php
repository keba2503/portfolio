<?php

namespace Hiperdino\Dinitos\Model\History\MovementTypes;

use Exception;
use Hiperdino\Dinitos\Model\Data\Package as PackageModel;
use Magento\Quote\Model\Quote;

class ExpirationMovement extends Movement
{
    const EXPIRATION_MOVEMENT = 2;

    public function handleMovement(?Quote $quote = null, $dinitos = null, ?PackageModel $package = null): bool
    {
        try {
            $balance = $this->customerDinitos->getCustomerDinitosTotal($package->getCustomerId()) - $package->getAvailableDinitos();
            $concept = str_replace('[increment_id]', $package->getIncrementId(), $this->configHelper->getTextConceptExpiration());
            $expirationMovement = $this->historyFactory->create();
            $expirationMovement->setCustomerId($package->getCustomerId());
            $expirationMovement->setConcept($concept);
            $expirationMovement->setIncrementId($package->getIncrementId());
            $expirationMovement->setDinitosQuantity($package->getAvailableDinitos());
            $expirationMovement->setPackageId($package->getId());
            $expirationMovement->setTransactionType(self::EXPIRATION_MOVEMENT);
            $expirationMovement->setDinitosBalance($balance);
            $this->historyRepository->save($expirationMovement);
        } catch (Exception $e) {
            $this->logger->logDinitosMovement(__("Error creando el movimiento de expiracion: \n {$e->getMessage()}"));

            return false;
        }

        return true;
    }

    public function handleAdminMovement($data)
    {
        // TODO: Implement handleAdminMovement() method.
    }

    public function revertMovement(mixed $customerId, mixed $incrementId, mixed $movementType, string $concept, mixed $movementData, mixed $newBalance)
    {
        // TODO: Implement revertMovement() method.
    }
}
