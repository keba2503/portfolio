<?php

namespace Hiperdino\Dinitos\Model\Services\Totals;

use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;

class ProductRewardDiscount
{
    public function getDiscountBySku($order, $sku)
    {
        $rewardsProduct = $this->getProductTypeRewards($order);
        if (!empty($rewardsProduct)) {
            foreach ($rewardsProduct as $reward) {
                $itemsSkus = array_keys($reward['items']);
                if (in_array($sku, $itemsSkus)) {
                    return [
                        'total_discount_amount' => $reward['items'][$sku]['total_discount_amount']
                    ];
                }
            }
        }

        return [
            'total_discount_amount' => 0
        ];
    }

    private function getProductTypeRewards($order)
    {
        $rewardsApplied = json_decode($order->getData('dinitos_rewards_discount_split') ?: "", true);
        $productsRewards = [];

        if (is_array($rewardsApplied) && key_exists('dinitos_rewards_discounts', $rewardsApplied)) {
            foreach ($rewardsApplied['dinitos_rewards_discounts'] as $reward) {
                if ($reward['reward']['type'] == RewardsTypeOptions::PRODUCT_VALUE) {
                    $productsRewards[] = $reward;
                }
            }
        }

        return $productsRewards;
    }
}