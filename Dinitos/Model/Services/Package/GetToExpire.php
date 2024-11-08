<?php

namespace Hiperdino\Dinitos\Model\Services\Package;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\ResourceModel\Package\CollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;

class GetToExpire
{
    public function __construct(
        protected CollectionFactory $collectionFactory,
        protected Logger $logger,
        protected Config $dinitosConfig
    ) {
    }

    public function getNextExpirablePackage($customerId): ?DataObject
    {
        $package = null;
        try {
            $date = $this->dinitosConfig->getDateFromExpirationFilterConfig();
            if ($date) {
                $dateToString = $date->format('Y-m-d H:i:s');
                $package = $this->collectionFactory->create()->addFieldToFilter('expiration_date', ['lteq' => $dateToString])->addCustomerRedeemableFilter($customerId)->addOrder('expiration_date', Collection::SORT_ORDER_ASC)->getFirstItem();
            } else {
                $package = $this->collectionFactory->create()->addCustomerRedeemableFilter($customerId)->addOrder('expiration_date', Collection::SORT_ORDER_ASC)->getFirstItem();
            }
        } catch (Exception $e) {
            $this->logger->logDinitosCustomer(__("Error al recuperar el paquete más próximo a expirar del cliente: \n {$e->getMessage()}"));
        }

        return $package;
    }
}