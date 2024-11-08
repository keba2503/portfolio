<?php

namespace Hiperdino\Dinitos\Model\Quote\Total;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\Totals\QuoteRewardsCalculator;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class RewardDiscount extends AbstractTotal
{
    public function __construct(
        protected Logger $logger,
        protected QuoteRewardsCalculator $quoteRewardsCalculator,
        protected Config $config
    ) {
        $this->setCode('dinitos_rewards_discount_amount');
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $storeId = $quote->getStoreId();
        if ($this->config->isDinitosAccumulatedEnabledWeb($storeId)) {
            $start_time = microtime(true);

            $this->logger->logMergeQuote(__($quote->getId() . " - Entro en Dinitos Rewards Total"));
            $items = $shippingAssignment->getItems();
            if (!count($items)) {
                $this->logger->logMergeQuote(__($quote->getId() . " - Salgo de Dinitos Rewards Total - No items"));

                return $this;
            }

            $quote->setSubtotal($total->getTotalAmount('subtotal'));
            $quote->getShippingAddress()->setDiscountAmount($total->getTotalAmount('discount'));
            $quote = $this->quoteRewardsCalculator->calculateRewards($quote);
            $amount = $quote->getData('dinitos_rewards_base_discount');

            $total->setTotalAmount($this->getCode(), -$amount);
            $total->setBaseTotalAmount($this->getCode(), -$amount);

            $quote->setData('dinitos_rewards_discount_amount', -$amount);
            $this->logger->logMergeQuote(__($quote->getId() . " - Salgo de Dinitos Rewards Total"));

            $end_time = microtime(true);
            $execution_time = $end_time - $start_time;
            $this->logger->logMergeQuote(__("Execution time of script = " . $execution_time . " sec"));
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => 10
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return $this->config->getRewardsTotalDiscountLabel();
    }
}