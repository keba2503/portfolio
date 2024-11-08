<?php

namespace Hiperdino\Dinitos\Model\Services\CustomerDinitos;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Magento\Customer\Model\Session;

class GetTotalDinitos
{
    const QTY_DEFAULT = 0;

    public function __construct(
        protected Logger $logger,
        protected GetDinitos $getCustomerDinitos,
        protected GetQuoteDinitos $getQuoteDinitos,
        protected Session $customerSession
    ) {
    }

    public function getTotalDinitosSum($customerId = null, $quote = null)
    {
        try {
            if (!$customerId) {
                $customerId = $this->customerSession->getCustomerId();
            }

            $customerDinitos = $this->getCustomerDinitos->getCustomerDinitosTotal($customerId);
            $customerDinitos = is_numeric($customerDinitos) ? $customerDinitos : self::QTY_DEFAULT;
            $quoteDinitos = $this->getQuoteDinitos->getCustomerCartDinitos($quote);
            $quoteDinitos = is_numeric($quoteDinitos) ? $quoteDinitos : self::QTY_DEFAULT;

            return $customerDinitos + $quoteDinitos;
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error en getTotalDinitosSum:\n {$e->getMessage()}");

            return self::QTY_DEFAULT;
        }
    }

}