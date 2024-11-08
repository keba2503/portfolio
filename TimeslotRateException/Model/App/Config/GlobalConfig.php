<?php

namespace Hiperdino\TimeslotRateException\Model\App\Config;

use Hiperdino\TimeslotRateException\Helper\Config;
use Singular\EcommerceApp\Api\Composite\GlobalConfigCompositeInterface;

class GlobalConfig implements GlobalConfigCompositeInterface
{
    public function __construct(
        protected readonly Config $timeslotRateExceptionConfig
    ) {
    }

    public function getInfo()
    {
        return [
            "timeslot_rate_exception_config" => [
                "show_shipping_method_price" => $this->timeslotRateExceptionConfig->getShowShippingMethodPrice()
            ]
        ];
    }
}
