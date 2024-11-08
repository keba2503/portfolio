<?php

namespace Hiperdino\Dinitos\Plugin;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;

class AfterShippingCollect
{
    const SHIPPING_FREE = 0;

    public function __construct(
        protected Config $configHelper,
        protected Registry $registry,
    ) {
    }

    public function afterCollectRates($carrier, $result)
    {
        /** @var Quote $quote */
        $quote = $this->registry->registry('quote_shipping_rates');
        if (!$quote) {
            return $result;
        }

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if ($shippingMethod) {
            list($carrierCode, $methodCode) = explode('_', $shippingMethod, 2);
            if ($carrier->getId() == $carrierCode) {
                $dinitosRewards = $quote->getData('dinitos_rewards');
                if ($dinitosRewards !== null) {
                    $dinitosRewards = json_decode($quote->getData('dinitos_rewards'), true);
                    $hasShippingReward = $this->checkForShippingReward($dinitosRewards);

                    if ($hasShippingReward) {
                        foreach ($result->getAllRates() as $rate) {
                            if ($rate->getMethod() == $methodCode) {
                                $originalPrice = $quote->getData('original_shipping_price');
                                if (!$originalPrice) {
                                    $quote->setData('original_shipping_price', $rate->getPrice());
                                    $originalPrice = $rate->getPrice();
                                }
                                $newPrice = $this->calculateNewShippingPrice($originalPrice);
                                $rate->setPrice($newPrice);
                                $rate->setCost($newPrice);

                                if ($newPrice == self::SHIPPING_FREE) {
                                    $rate->setData("dinitos_free", 1);
                                    $quote->setData('free_shipping_method_indicator', 'dinitos_acumulated');
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function checkForShippingReward($dinitosRewards)
    {
        if (is_array($dinitosRewards)) {
            foreach ($dinitosRewards as $reward) {
                if (isset($reward['type']) && $reward['type'] == RewardsTypeOptions::SHIPPING_VALUE) {
                    return true;
                }
            }
        }

        return false;
    }

    private function calculateNewShippingPrice($originalPrice)
    {
        $shippingPercentage = $this->configHelper->getRewardsShippingPercentage();
        $discountAmount = ($shippingPercentage / 100) * $originalPrice;

        return max($originalPrice - $discountAmount, 0);
    }

}