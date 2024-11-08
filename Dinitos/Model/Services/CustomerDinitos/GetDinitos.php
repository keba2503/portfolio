<?php

namespace Hiperdino\Dinitos\Model\Services\CustomerDinitos;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

class GetDinitos
{
    const QTY_DEFAULT = 0;

    public function __construct(
        protected Logger $logger,
        protected Session $customerSession,
        protected CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function getCustomerDinitosTotal($customerId)
    {
        try {
            if (!$customerId) {
                $customerId = $this->customerSession->getCustomerId();
            }
            $customer = $this->customerRepository->getById($customerId);

            return $customer->getCustomAttribute('dinitos') ? $customer->getCustomAttribute('dinitos')->getValue() : self::QTY_DEFAULT;
        } catch (Exception $e) {
            $this->logger->logDinitosService(__("Error al obtener los dinitos del usuario $customerId: \n {$e->getMessage()}"));

            return self::QTY_DEFAULT;
        }
    }
}