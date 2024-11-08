<?php

namespace Hiperdino\TimeslotRateException\Plugin;

use Hiperdino\Checkout\Block\Cart;
use Hiperdino\TimeslotRateException\Helper\Config;

class ShippingPriceAfter
{
    public function __construct(
        protected readonly Config $timeslotRateExceptionConfig
    ) {
    }

    public function afterGetShippingPrice(Cart $subject, $result)
    {
        if ($this->timeslotRateExceptionConfig->getShowTimeslotPrice()) {
            return "";
        }

        return $result;
    }
}