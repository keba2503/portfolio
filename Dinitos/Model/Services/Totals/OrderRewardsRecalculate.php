<?php

namespace Hiperdino\Dinitos\Model\Services\Totals;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;

class OrderRewardsRecalculate
{
    public function __construct(
        protected Config $dinitosHelper
    ) {
    }

    public function recalculateOrderDinitosRewards($order)
    {
        $messages = [];
        $rewardsTotalDiscount = 0;
        $rewardsToUse = $this->getOrderDinitosRewards($order);
        $existingRewardsDataJson = $order->getData("dinitos_rewards_discount_split");
        $existingRewardsData = $existingRewardsDataJson ? json_decode($existingRewardsDataJson, true) : [];
        $rewardsSplit = $existingRewardsData['dinitos_rewards_discounts'] ?? [];

        if ($rewardsToUse) {
            foreach ($rewardsToUse as $rewardId => $reward) {
                if ((int)$reward['type'] === RewardsTypeOptions::PRODUCT_VALUE) {
                    $rewardApplication = $this->applyRewardInvoice($reward, $order);
                    if (!$rewardApplication['applied']) {
                        $messages[] = "⚠️ La recompensa de dinitos $rewardId no se pudo aplicar";
                        continue;
                    }

                    $rewardsTotalDiscount += $rewardApplication['amount'];

                    $foundIndex = null;
                    foreach ($rewardsSplit as $index => $existingReward) {
                        if ($existingReward['reward']['id'] == $rewardId) {
                            $foundIndex = $index;
                            break;
                        }
                    }

                    if (null !== $foundIndex) {
                        $rewardsSplit[$foundIndex] = [
                            'reward' => $reward,
                            'amount' => $rewardApplication['amount'],
                            'percentage' => $rewardApplication['percentage'],
                            'items' => $rewardApplication['items']
                        ];
                    } else {
                        $rewardsSplit[] = [
                            'reward' => $reward,
                            'amount' => $rewardApplication['amount'],
                            'percentage' => $rewardApplication['percentage'],
                            'items' => $rewardApplication['items']
                        ];
                    }
                }
            }
        }

        $updatedRewardsData = [
            'dinitos_rewards_discounts' => $rewardsSplit
        ];
        $updatedRewardsDataJson = json_encode($updatedRewardsData);

        $order->setData("dinitos_rewards_discount_split", $updatedRewardsDataJson);
        $order->setData("dinitos_rewards_base_discount", $rewardsTotalDiscount);
        $order->setData("dinitos_rewards_discount_amount", $rewardsTotalDiscount);
        $order->setData("dinitos_rewards_messages", $messages);

        return $order;
    }

    private function getOrderDinitosRewards($order)
    {
        return ($order->getDinitosRewards() && !is_null($order->getDinitosRewards())) ? json_decode($order->getDinitosRewards(), associative: true) : [];
    }

    private function applyRewardInvoice($reward, $order)
    {
        $result = $this->isProductInOrder($reward['entity_identifier'], $order);

        if (!is_bool($result) && !$result['success']) {

            return ['applied' => false];
        }

        $discount = $this->calculateRewardsDiscount($reward, $order, $result['products'] ?? []);

        return [
            'applied' => true,
            'reward' => $reward,
            'percentage' => $discount['percentage'],
            'amount' => $discount['amount'],
            'items' => $discount['items']
        ];
    }

    private function isProductInOrder($sku, $order)
    {
        $otherProducts = $this->dinitosHelper->getApplyRewardOthersProducts();
        $productsSkus = explode(",", $sku);
        $bagsFound = array_intersect($productsSkus, $this->dinitosHelper->getBagSkus());
        if (empty($bagsFound) && !$otherProducts) {

            return [
                'success' => false
            ];
        }

        $productsFound = [];
        foreach ($order->getAllVisibleItems() as $item) {
            if (in_array($item->getSku(), $productsSkus)) {
                $productsFound[] = $item;
            }
        }

        return [
            'success' => !empty($productsFound),
            'products' => $productsFound
        ];
    }

    private function calculateRewardsDiscount($reward, $order, $products = [])
    {

        $totalDiscountAmount = 0;
        $items = [];
        $percentage = 0;

        if (!empty($products)) {
            $productPercentage = $this->dinitosHelper->getRewardsProductsPercentage();
            $percentage = $productPercentage;
            foreach ($products as $product) {
                $productPrice = $product->getPrice();
                $productTotalPrice = $productPrice * $product->getQtyOrdered();
                $discountAmount = ($productPercentage / 100) * $productTotalPrice;
                $totalDiscountAmount += $discountAmount;

                $items[$product->getSku()] = [
                    'total_discount_amount' => $discountAmount,
                    'item_qty' => $product->getQtyOrdered(),
                    'item_price' => $productPrice
                ];
            }
        }

        return [
            'percentage' => $percentage,
            'amount' => $totalDiscountAmount,
            'items' => $items
        ];
    }
}