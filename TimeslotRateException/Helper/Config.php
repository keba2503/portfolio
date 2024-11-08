<?php

namespace Hiperdino\TimeslotRateException\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const HIPERDINO_TIMESLOT_RATE_EXCEPTION_GENERAL_SHOW_SHIPPING_METHOD_PRICE = 'hiperdino_timeslot_rate_exception/general/show_shipping_method_price';

    public function __construct(
        protected readonly StoreManagerInterface $storeManager,
        protected readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getShowShippingMethodPrice()
    {
        return $this->getValue(self::HIPERDINO_TIMESLOT_RATE_EXCEPTION_GENERAL_SHOW_SHIPPING_METHOD_PRICE);
    }

    public function getShowTimeslotPrice()
    {
        return !$this->getValue(self::HIPERDINO_TIMESLOT_RATE_EXCEPTION_GENERAL_SHOW_SHIPPING_METHOD_PRICE);
    }

    private function getValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }
}

