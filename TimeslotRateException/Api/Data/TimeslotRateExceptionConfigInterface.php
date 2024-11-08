<?php

namespace Hiperdino\TimeslotRateException\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Config timeslot rate exception interface.
 * @api
 */
interface TimeslotRateExceptionConfigInterface extends CustomAttributesDataInterface
{
    const SHOW_SHIPPING_METHOD_PRICE = 'show_shipping_method_price';

    /**
     * @return bool
     */
    public function getShowShippingMethodPrice();

    /**
     * @param bool $showShippingMethodPrice
     * @return $this
     */
    public function setShowShippingMethodPrice($showShippingMethodPrice);
}
