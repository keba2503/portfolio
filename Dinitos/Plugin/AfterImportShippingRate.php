<?php

namespace Hiperdino\Dinitos\Plugin;


use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class AfterImportShippingRate
{
    public function afterImportShippingRate(Rate $subject, $result, AbstractResult $rate)
    {
        if ($rate instanceof Method) {
            $result->setData('dinitos_free', $rate->getData('dinitos_free'));
        }

        return $result;
    }
}
