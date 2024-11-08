<?php

namespace Hiperdino\Dinitos\Plugin;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\Rewards\GetCouponCodes;
use Hiperdino\Dinitos\Model\Services\Rewards\GetQuote;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Singular\EcommerceApp\Helper\Cart;

class AfterApplyCoupon
{
    public function __construct(
        protected GetQuote $customerRewards,
        protected Cart $cartHelper,
        protected Logger $logger,
        protected GetCouponCodes $discountRewardsCodes
    ) {
    }

    public function afterApplyMagentoCoupon($subject, $result)
    {
        try {
            $quote = $this->cartHelper->getQuote();
            if (!$this->isDiscountRewardCode($quote->getCouponCode()) && $result['success']) {
                $quoteRewards = $quote->getDinitosRewards() ?
                    json_decode($quote->getDinitosRewards(), associative: true) : [];
                if (empty($quoteRewards)) {
                    return $result;
                }
                $filteredRewards = array_filter($quoteRewards, function ($reward) {
                    if ($reward['type'] != RewardsTypeOptions::DISCOUNT_VALUE) {
                        return $reward;
                    }
                });
                $this->customerRewards->setRewardsToQuote($filteredRewards);
            }
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error en AfterApplyCoupon: \n {$e->getMessage()}");
        }

        return $result;
    }

    private function isDiscountRewardCode($code): bool
    {
        $codes = $this->discountRewardsCodes->getAllCodes();

        return in_array($code, $codes);
    }
}