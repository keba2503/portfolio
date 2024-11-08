<?php

namespace Hiperdino\Dinitos\Model\Services\Package;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\ResourceModel\Package\CollectionFactory;

class GetRedeemable
{
    public function __construct(
        protected CollectionFactory $collectionFactory,
        protected Logger $logger
    ) {
    }

    public function getRedeemablePackagesFromCustomer($customerId): array
    {
        $packages = [];
        try {
            $packages = $this->collectionFactory->create()->addCustomerRedeemableFilter($customerId)->getItems();
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error al recuperar el histórico del cliente: \n {$e->getMessage()}"));
        }

        return $packages;
    }
}