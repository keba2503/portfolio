<?php

namespace Hiperdino\Dinitos\Block;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\Rewards\GetAvailable;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Singular\EcommerceApp\Helper\Cart;
use Singular\EcommerceApp\Helper\Cart as ExtraInfo;

class CartRewardInfo extends Template
{
    public function __construct(
        protected Config $config,
        protected Session $customerSession,
        protected GetAvailable $storeViewRewards,
        protected GetDinitos $customerBalance,
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
        return $this->storeViewRewards->getStoreViewRewards('0');
    }

    /**
     * @throws Exception
     */
    public function getCustomerDinitosBalance(): array
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

    public function getDinitosRewardsFromQuote()
    {
        $dataFromTable = $this->extraInfo->getQuote()->getData("dinitos_rewards");

        return $dataFromTable ? json_decode($dataFromTable, true) : [];
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

    /**
     * @throws Exception
     */
    public function mapRewards()
    {
        try {
            $rewards = $this->getStoreViewActiveRewards();
            $customerBalance = $this->getCustomerDinitosBalance();
            $excludeIds = [];
            $excludeSelectedRewards = $this->config->excludeSelectedRewards();

            if (isset($customerBalance['net'])) {
                $balance = $customerBalance['net'];
                $reachableRewards = [];
                $unreachableRewards = [];

                if ($excludeSelectedRewards) {
                    $excludeIds = array_map(function ($reward) {
                        return $reward['id'];
                    }, $this->getDinitosRewardsFromQuote());
                }

                if ($rewards !== null) {
                    foreach ($rewards as $reward) {
                        if (!in_array($reward->getId(), $excludeIds)) {
                            if ($reward->getDinitosQty() <= $balance) {
                                $reachableRewards[] = $reward;
                            } else {
                                $unreachableRewards[] = $reward;
                            }
                        }
                    }
                }

                usort($unreachableRewards, function ($a, $b) {
                    return $a->getDinitosQty() - $b->getDinitosQty();
                });

                return [
                    'reachable' => $reachableRewards,
                    'unreachable' => $unreachableRewards
                ];

            } else {
                throw new Exception("Invalid customer balance data.");
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->logDinitosService(__($errorMessage));
            throw new Exception($errorMessage);
        }
    }

    public function getMinQtyFromUnreachableRewards($unreachable = [])
    {
        return array_reduce($unreachable, function ($carry, $item) {
            $qty = $item->getDinitosQty();
            if ($carry === null || $qty < $carry) {
                return $qty;
            }

            return $carry;
        });
    }

    public function getNearestReward($unreachable = [])
    {
        $minDinitosQty = $this->getMinQtyFromUnreachableRewards($unreachable);

        return array_filter($unreachable, function ($item) use ($minDinitosQty) {
            return $item->getDinitosQty() == $minDinitosQty;
        });
    }

    public function showShortfallNextReward($unreachable = [])
    {
        return abs($this->getCustomerDinitosBalance()['net'] - $this->getMinQtyFromUnreachableRewards($unreachable));
    }

    public function joinRewards($rewards, $andOr = 'y')
    {
        $count = count($rewards);
        $output = '';

        foreach ($rewards as $key => $item) {
            $output .= $item->getCheckoutText();
            if ($key < $count - 2) {
                $output .= ', ';
            } elseif ($key == $count - 2) {
                $output .= " {$andOr} ";
            }
        }

        return $output;
    }

    public function getShortfallText()
    {
        return $this->config->getDinitosLeftText();
    }

    public function getDinitosToUseText()
    {
        return $this->config->getDinitosToUseInText();
    }

    public function getDinitosToAchieveText()
    {
        return $this->config->getDinitosToAchieveText();
    }

    public function getDinitosObtainedText()
    {
        return $this->config->getDinitosObtainedText();
    }

}