<?php

namespace Hiperdino\BlackFriday\Observer;

use Magento\Framework\Event\ObserverInterface;

class MergerQuoteAfter implements ObserverInterface
{

    protected $_registry;

    /**
     * @var \Hiperdino\BlackFriday\Helper\Data
     */
    private $_blackfridayHelper;


    public function __construct(
        \Magento\Framework\Registry $registry,
        \Hiperdino\BlackFriday\Helper\Data $blackfridayHelper
    )
    {
        $this->_registry = $registry;
        $this->_blackfridayHelper = $blackfridayHelper;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $cart */
        $quote = $observer->getEvent()->getData('quote');

        $this->_blackfridayHelper->recalculateDiscount($quote);

        return $this;
    }
}
