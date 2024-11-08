<?php

namespace Hiperdino\Dinitos\Observer;

use Exception;
use Hiperdino\Dinitos\Model\Services\Rewards\SetQuote;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateDinitosRewardsBeforeCollectTotals implements ObserverInterface
{

    public function __construct(
        protected SetQuote $filterQuoteRewards
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            $quote = $observer->getEvent()->getData('quote');
            if ($quote->getDinitosRewards()) {
                $this->filterQuoteRewards->setQuoteRewards($quote);
            }
        } catch (Exception $e) {}

        return $this;
    }
}