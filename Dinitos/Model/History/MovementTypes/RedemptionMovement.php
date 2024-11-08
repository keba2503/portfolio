<?php

namespace Hiperdino\Dinitos\Model\History\MovementTypes;

use Exception;
use Hiperdino\Dinitos\Model\Data\Package as PackageModel;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;

class RedemptionMovement extends Movement
{
    function handleMovement(?Quote $quote = null, $dinitos = null, ?PackageModel $package = null): bool
    {
        try {
            $redeemedIds = $this->redeemCustomerPackages($dinitos, $quote);
            $this->extracted($dinitos, $redeemedIds, $quote);
        } catch (Exception $e) {
            $this->movementLog(__("Error creando el movimiento de redención: \n {$e->getMessage()}"));

            return false;
        }

        return true;
    }

    public function handleAdminMovement($data)
    {
        try {
            $dinitos = $data['dinitos_quantity'];
            $redeemedIds = $this->redeemCustomerPackages(abs($dinitos), null, $data);
            $this->extracted($dinitos, $redeemedIds, null, $data);
        } catch (Exception $e) {
            $this->movementLog(__("Error creando el movimiento de redención desde el admin: \n {$e->getMessage()}"));

            return false;
        }

        return true;
    }

    /**
     * @param $dinitos
     * @param array $redeemedIds
     * @param Quote|null $quote
     * @param null $data
     * @return void
     * @throws CouldNotSaveException
     */
    public function extracted($dinitos, array $redeemedIds, Quote $quote = null, $data = null): void
    {
        $incrementId = $quote ? $quote->getReservedOrderId() : $data['increment_id'];
        $customerId = $quote ? $quote->getCustomerId() : $data['customer_id'];
        $concept = str_replace('[increment_id]', $incrementId, $this->configHelper->getTextConceptRedemption());
        $concept = str_replace('[increment_id]', '', $concept);
        if (!$quote) {
            $concept = $data['concept'];
        }
        $customerDinitosAttribute = $this->customerDinitos->getCustomerDinitosTotal($customerId);
        $redemptionMovement = $this->historyFactory->create();
        $redemptionMovement->setCustomerId($customerId);
        $redemptionMovement->setConcept($concept);
        $redemptionMovement->setIncrementId($incrementId);
        $redemptionMovement->setDinitosQuantity($dinitos);
        $redemptionMovement->setTransactionType(self::REDEMPTION_MOVEMENT);
        $redemptionMovement->setPackageId(implode(',', $redeemedIds));
        $balance = $quote ?
            $customerDinitosAttribute + $quote->getTotalDinitosQty() - $dinitos :
            $customerDinitosAttribute - abs($dinitos);
        $redemptionMovement->setDinitosBalance($balance);
        $this->historyRepository->save($redemptionMovement);
    }

    private function redeemCustomerPackages($dinitos, $quote = null, $data = null)
    {
        try {

            $dinitosFromCustomerPackages = 0;
            $redeemedIds = [];
            $customerId = $quote ? $quote->getCustomerId() : $data['customer_id'];
            $customerPackages = array_values($this->customerReedemablePackages->getRedeemablePackagesFromCustomer($customerId));
            foreach ($customerPackages as $package) {
                $dinitosFromCustomerPackages += $package->getAvailableDinitos();
                $redeemedIds[] = $package->getId();

                if ($dinitosFromCustomerPackages < $dinitos) {
                    $this->packageService->updatePackage($package, 0, self::REDEEMED_VALUE);
                } else {
                    $packageLeft = $dinitosFromCustomerPackages - $dinitos;
                    $redeemed = !$packageLeft ?: 0;
                    $this->packageService->updatePackage($package, $packageLeft, $redeemed);
                    break;
                }
            }
        } catch (Exception $e) {
            $this->logger->logDinitosMovement("Error redimiendo los paquetes del customer: \n {$e->getMessage()}");

            return [];
        }

        return $redeemedIds;
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