<?php

namespace Hiperdino\Anniversary\Observer;

use Exception;
use Hiperdino\Anniversary\Model\Participation\ParticipationCalculator;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CartSaveAfter implements ObserverInterface
{
    protected ParticipationCalculator $anniversaryHelper;

    public function __construct(
        ParticipationCalculator $anniversaryHelper
    ) {
        $this->anniversaryHelper = $anniversaryHelper;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getData('cart');

        if ($cart) $quote = $cart->getQuote();
        else $quote = $observer->getEvent()->getData('quote');

        $this->anniversaryHelper->recalculateRascas($quote);

        return $this;
    }
}
