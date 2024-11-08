<?php

namespace Hiperdino\Anniversary\Observer;

use Exception;
use Hiperdino\Anniversary\Model\Participation\ParticipationCalculator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

class MergerQuoteAfter implements ObserverInterface
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
        /** @var Quote $cart */
        $quote = $observer->getEvent()->getData('quote');

        $this->anniversaryHelper->recalculateRascas($quote);

        return $this;
    }
}
