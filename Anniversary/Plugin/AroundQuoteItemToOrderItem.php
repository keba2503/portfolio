<?php

namespace Hiperdino\Anniversary\Plugin;

use Closure;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;

class AroundQuoteItemToOrderItem
{
    public function aroundConvert(
        ToOrderItem $subject,
        Closure $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        $orderItem->setData('is_anniversary_product', $item->getData('is_anniversary_product'));

        return $orderItem;
    }
}
