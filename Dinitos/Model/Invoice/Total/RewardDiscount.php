<?php

namespace Hiperdino\Dinitos\Model\Invoice\Total;

use Hiperdino\Dinitos\Helper\Logger;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class RewardDiscount extends AbstractTotal
{
    public function __construct(
        protected Logger $logger
    ) {
        parent::__construct();
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $this->logger->logDinitosRewardsInvoiceTotals($invoice->getOrder()->getIncrementId() . " - Entro en Dinitos Rewards Invoice Total");

        $rewardsDiscount = $invoice->getOrder()->getData('dinitos_rewards_discount_amount');

        $invoice->setData('dinitos_rewards_discount_amount', $rewardsDiscount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $rewardsDiscount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $rewardsDiscount);

        $this->logger->logDinitosRewardsInvoiceTotals($invoice->getOrder()->getIncrementId() . " - Entro en Dinitos Rewards Invoice Total");

        return $this;
    }
}