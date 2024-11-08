<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Rewards\DiscountManager;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetTotalDinitos;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Quote\Model\QuoteRepository;
use Singular\EcommerceApp\Helper\Cart;

class SetQuote
{
    public function __construct(
        protected QuoteRepository $quoteRepository,
        protected Cart $cartHelper,
        protected GetQuote $customerRewards,
        protected GetTotalDinitos $getCustomerAndQuoteDinitos,
        protected DiscountManager $discountReward,
        protected Logger $logger,
        protected Config $config
    ) {
    }

    public function setQuoteRewards($quote): void
    {
        $enabled = $this->config->isDinitosAccumulatedEnabledWeb();

        try {
            if (!$quote) {
                $quote = $this->cartHelper->getQuote();
            }
            if (!$quote || !$quote->getDinitosRewards()) {
                return;
            }
            $quoteRewards = $quote->getData('dinitos_rewards') ? json_decode($quote->getData('dinitos_rewards'), associative: true) : [];
            $rewardsQuoteDinitosQty = $this->customerRewards->getQuoteRewardsDinitos($quoteRewards);
            $totalDninitosQty = $this->getCustomerAndQuoteDinitos->getTotalDinitosSum(null, $quote);
            $rewardsToUse = [];
            if ($totalDninitosQty < $rewardsQuoteDinitosQty) {
                foreach ($quoteRewards as $reward) {
                    if (($totalDninitosQty - $reward['dinitos_qty']) >= 0) {
                        $totalDninitosQty -= $reward['dinitos_qty'];
                        $rewardsToUse[] = $reward;
                    } else {
                        if ($reward['type'] == RewardsTypeOptions::DISCOUNT_VALUE) {
                            $this->discountReward->removeCoupon();
                        }
                    }
                }
            } else {
                $rewardsToUse = $quoteRewards;
            }
            !empty($rewardsToUse) ?
                $quote->setData('dinitos_rewards', json_encode($rewardsToUse)) :
                $quote->setData('dinitos_rewards');

            $this->dinitosDisabled($enabled, $quote);

        } catch (Exception $e) {
            $this->logger->logFilterDinitosReward(__("Error: {$e->getMessage()}"));
        }
    }

    /**
     * @param bool $enabled
     * @param mixed $quote
     * @return void
     */
    public function dinitosDisabled(bool $enabled, mixed $quote): void
    {
        if (!$enabled) {
            $quote->setData('dinitos_rewards', null);
            $quote->setData('dinitos_rewards_discount_split', null);
            $quote->setData('dinitos_rewards_base_discount', null);
            $quote->setData('dinitos_rewards_discount_amount', null);
            $this->discountReward->removeCoupon();
        }
    }
}