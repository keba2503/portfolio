<?php

namespace Hiperdino\Dinitos\Block;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Quote\Total\RewardDiscount;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\Rewards\GetAvailable;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Singular\EcommerceApp\Helper\Cart;
use Singular\EcommerceApp\Helper\Cart as ExtraInfo;

class Rewards extends Template
{
    public function __construct(
        protected Config $config,
        protected Session $customerSession,
        protected GetAvailable $storeViewRewards,
        protected GetDinitos $customerBalance,
        protected RewardDiscount $rewardDiscount,
        protected Cart $cart,
        protected Quote $quote,
        protected Total $total,
        protected ExtraInfo $extraInfo,
        protected Logger $logger,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isDinitosAccumulatedEnabledWeb()
    {
        return $this->config->isDinitosAccumulatedEnabledWeb();
    }

    public function getStoreViewActiveRewards()
    {
        return $this->storeViewRewards->getStoreViewRewards();
    }

    /**
     * @throws Exception
     */
    public function getCustomerDinitosBalance()
    {
        try {
            $customerId = $this->customerSession->getCustomerId();
            $historyBalance = $this->customerBalance->getCustomerDinitosTotal($customerId);
            $obtainedDinitosByProductsInCart = $this->cart->getQuote()->getTotalDinitosQty();
            $rewards = $this->getDinitosRewardsFromQuote();
            $dinitosInQuote = $this->getTotalDinitosFromQuote($rewards);

            return [
                'history' => $historyBalance,
                'cart' => $obtainedDinitosByProductsInCart,
                'available' => $historyBalance + $obtainedDinitosByProductsInCart,
                'quote' => $dinitosInQuote,
                'net' => $historyBalance + $obtainedDinitosByProductsInCart - $dinitosInQuote
            ];

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->logDinitosService(__($errorMessage));
            throw new Exception($errorMessage);
        }
    }

    public function enoughToGetReward($qty = 0)
    {
        return ($this->getCustomerDinitosBalance()['net'] - (int)$qty) >= 0;
    }

    public function getDinitosRewardsFromQuote()
    {
        $dataFromTable = $this->extraInfo->getQuote()->getData("dinitos_rewards");

        return $dataFromTable ? json_decode($dataFromTable, true) : [];
    }

    public function lookForRewardsInQuote($quoteRewards, $rewardId)
    {
        if ($quoteRewards !== null) {
            foreach ($quoteRewards as $object) {
                if ($object['id'] === $rewardId) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTotalDinitosFromQuote($quoteRewards)
    {
        $totalQty = 0;
        if ($quoteRewards !== null) {
            foreach ($quoteRewards as $object) {
                if (isset($object['dinitos_qty'])) {
                    $totalQty += $object['dinitos_qty'];
                }
            }
        }

        return $totalQty;
    }

    public function getDinitosRewardsTexts()
    {
        return $this->config->getRewardTexts();
    }

    public function getRewardDiscountType()
    {
        return RewardsTypeOptions::DISCOUNT_VALUE;
    }

    public function parseRewardText($rewardText, $dinitos_qty)
    {
        $placeholder = '[dinitos_qty]';

        return str_replace($placeholder, $dinitos_qty, $rewardText);
    }

    public function getErrorMessage()
    {
        return $this->config->getRewardsErrorMessage();
    }

}