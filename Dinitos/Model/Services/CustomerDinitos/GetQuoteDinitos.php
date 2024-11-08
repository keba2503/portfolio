<?php

namespace Hiperdino\Dinitos\Model\Services\CustomerDinitos;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Singular\EcommerceApp\Helper\Cart;

class GetQuoteDinitos
{
    const DEFAULT_DINITOS_VALUE = 0;

    public function __construct(
        protected Cart $cartHelper,
        protected Logger $logger
    ) {
    }

    public function getCustomerCartDinitos($quote = null)
    {
        try {
            if (!$quote) {
                $quote = $this->cartHelper->getQuote();
            }

            return $quote->getTotalDinitosQty() ?: self::DEFAULT_DINITOS_VALUE;
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error al obtener los dinitos del carrito: \n {$e->getMessage()}");
        }
    }
}
