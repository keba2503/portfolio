<?php

namespace Hiperdino\Dinitos\Model\Services\CustomerDinitos;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

class SetDinitos
{
    public function __construct(
        protected Logger $logger,
        protected Session $customerSession,
        protected CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function setCustomerDinitos($customerId, $dinitosQty): void
    {
        try {
            if (!$customerId) {
                $customerId = $this->customerSession->getCustomerId();
            }
            $customer = $this->customerRepository->getById($customerId)->setCustomAttribute('dinitos', $dinitosQty);
            $this->customerRepository->save($customer);
        } catch (Exception $e) {
            $this->logger->logDinitosService(__("Error al guardar los dinitos del usuario $customerId: \n {$e->getMessage()}"));
        }
    }
}