<?php

namespace Hiperdino\Dinitos\Model\Services\Shipping;

use Singular\EcommerceApp\Helper\Cart;

class UpdateFreeShippingIndicator
{
    const SHIPPING_FREE = 0;

    public function __construct(
        protected Cart $cartHelper
    ) {
    }

    public function execute($indicator = null): void
    {
        $quote = $this->cartHelper->getQuote();
        $quote->setData('free_shipping_method_indicator', $indicator);
    }

    public function getFreeShippingIndicator()
    {
        $quote = $this->cartHelper->getQuote();

        return $quote->getData('free_shipping_method_indicator');
    }
}