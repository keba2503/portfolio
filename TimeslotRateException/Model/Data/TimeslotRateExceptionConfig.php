<?php

namespace Hiperdino\TimeslotRateException\Model\Data;

use Hiperdino\TimeslotRateException\Api\Data\TimeslotRateExceptionConfigInterface;
use Magento\Framework\Api\AbstractExtensibleObject as AbstractExtensibleObject;

class TimeslotRateExceptionConfig extends AbstractExtensibleObject implements TimeslotRateExceptionConfigInterface
{

    public function getShowShippingMethodPrice()
    {
        return $this->_get(self::SHOW_SHIPPING_METHOD_PRICE);
    }

    public function setShowShippingMethodPrice($showShippingMethodPrice)
    {
        return $this->setData(self::SHOW_SHIPPING_METHOD_PRICE, $showShippingMethodPrice);
    }
}
