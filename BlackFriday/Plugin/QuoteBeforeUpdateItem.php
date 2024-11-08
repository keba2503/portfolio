<?php

namespace Hiperdino\BlackFriday\Plugin;

class QuoteBeforeUpdateItem
{

    /**
     * @param \Magento\Quote\Model\Quote $quote $quote
     * @param int $itemId
     * @param \Magento\Framework\DataObject $buyRequest
     * @param null|array|\Magento\Framework\DataObject $params
     */
    public function beforeUpdateItem(
        $quote,
        $itemId,
        $buyRequest,
        $params = null
    ) {

    }
}
