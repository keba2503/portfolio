<?php

namespace Hiperdino\Dinitos\Model\Services\Totals;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Hiperdino\Dinitos\Model\Rewards\DiscountManager;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetTotalDinitos;
use Hiperdino\Dinitos\Model\Services\Rewards\GetQuote;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SalesRule\Model\CouponRepository;
use Magento\SalesRule\Model\RuleRepository;

class QuoteRewardsCalculator
{
    const DISCOUNT = 0;

    public function __construct(
        protected GetQuote $customerRewards,
        protected RewardRepository $rewardRepository,
        protected Config $dinitosHelper,
        protected CouponRepository $couponRepository,
        protected RuleRepository $ruleRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected GetTotalDinitos $getCustomerAndQuoteDinitos,
        protected DiscountManager $discountReward,
    ) {
    }

    public function calculateRewards($quote)
    {
        $rewardsTotalDiscount = 0;
        $rewardsSplit = [];
        $rewardsToUse = ($quote->getDinitosRewards() && !is_null($quote->getDinitosRewards())) ? json_decode($quote->getDinitosRewards(), associative: true) : [];
        foreach ($rewardsToUse as $reward) {
            $rewardApplication = $this->applyReward($reward, $quote);
            if (!$rewardApplication['applied']) {
                continue;
            }
            [$rewardsTotalDiscount, $rewardApplicationEntry] = $this->rewardApplication($rewardApplication, $rewardsTotalDiscount);
            $rewardsSplit = $this->updateEntry($rewardApplication, $rewardApplicationEntry, $rewardsSplit);
        }
        $quote->setData("dinitos_rewards_discount_split", json_encode(['dinitos_rewards_discounts' => $rewardsSplit]));
        $quote->setData("dinitos_rewards_base_discount", $rewardsTotalDiscount);

        return $quote;
    }

    /**
     * Handle reward application and update total discount.
     *
     * @param array $rewardApplication
     * @param mixed $totalDiscount
     * @return array
     */
    private function rewardApplication(array $rewardApplication, mixed $totalDiscount): array
    {
        $totalDiscount += $rewardApplication['amount'];
        $rewardEntry = [
            'reward' => $rewardApplication['reward'],
            'amount' => $rewardApplication['amount']
        ];

        return [$totalDiscount, $rewardEntry];
    }

    /**
     * @param array $rewardApplication
     * @param array $rewardEntry
     * @param array $rewardsSplit
     * @return array
     */
    private function updateEntry(array $rewardApplication, array $rewardEntry, array $rewardsSplit): array
    {
        $rewardEntry['percentage'] = (!empty($rewardApplication['percentage']) && $rewardApplication['percentage'] > self::DISCOUNT) ? $rewardApplication['percentage'] : null;
        $rewardEntry['items'] = (!empty($rewardApplication['items'])) ? $rewardApplication['items'] : [];

        if (!empty($rewardApplication['coupon'])) {
            $rewardEntry['coupon'] = $rewardApplication['coupon'];
        }

        $rewardsSplit[] = $rewardEntry;

        return $rewardsSplit;
    }

    private function applyReward($reward, $quote)
    {
        $result = $this->isRewardApplicable($reward, $quote);

        if (!is_bool($result) && !$result['success']) {
            return ['applied' => false];
        }

        $discount = $this->calculateRewardsDiscount($reward, $quote, $result['products'] ?? [], $result['coupon'] ?? '');

        $array = [
            'applied' => true,
            'reward' => $reward,
            'amount' => $discount['amount']
        ];

        if (!empty($discount['percentage'] && $discount['percentage'] > self::DISCOUNT)) {
            $array['percentage'] = $discount['percentage'];
            $array['items'] = $discount['items'];
        } else if (!empty($discount['coupon'])) {
            $array['coupon'] = $discount['coupon'];
        }

        return $array;
    }

    private function isRewardApplicable($reward, $quote)
    {
        if (!$reward['active']) {
            return [
                'success' => false,
            ];
        }

        if ((int)$reward['type'] === RewardsTypeOptions::PRODUCT_VALUE) {
            return $this->isProductInQuote($reward['entity_identifier'], $quote);
        }

        if ((int)$reward['type'] === RewardsTypeOptions::SHIPPING_VALUE) {
            return [
                'success' => true,
            ];
        }

        if ((int)$reward['type'] === RewardsTypeOptions::DISCOUNT_VALUE) {
            $couponCode = $reward['entity_identifier'];
            $couponRule = $this->getCartRuleByCouponCode($couponCode);
            $couponDiscount = 0;
            $active = false;
            if ($couponRule && $couponRule->getIsActive()) {
                $couponDiscount = $couponRule->getDiscountAmount();
                $active = true;
            }

            return [
                'success' => $couponDiscount > self::DISCOUNT && $active,
                'coupon' => $active ? $couponRule : ''
            ];
        }

        return [
            'success' => false,
        ];
    }

    private function isProductInQuote($sku, $quote)
    {
        $otherProducts = $this->dinitosHelper->getApplyRewardOthersProducts();
        $productsSkus = explode(",", $sku);
        $bagsFound = array_intersect($productsSkus, $this->dinitosHelper->getBagSkus());
        if (empty($bagsFound) && !$otherProducts) {
            return [
                'success' => false
            ];
        } else if (!empty($bagsFound)) {

            return [
                'success' => false
            ];
        }
        $productsFound = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getSku(), $productsSkus)) {
                $productsFound[] = $item;
            }
        }

        return [
            'success' => !empty($productsFound),
            'products' => $productsFound
        ];
    }

    private function getCartRuleByCouponCode($couponCode)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('code', $couponCode)->create();
        $couponList = $this->couponRepository->getList($searchCriteria);

        if ($couponList->getTotalCount()) {
            $items = $couponList->getItems();
            $coupon = reset($items);
            $ruleId = $coupon->getRuleId();

            return $this->ruleRepository->getById($ruleId);
        }

        return null;
    }

    private function calculateRewardsDiscount($reward, $quote, $products = [], $coupon = '')
    {
        $totalDiscountAmount = 0;
        $items = [];
        $percentage = 0;

        [$percentage, $totalDiscountAmount, $items] = $this->discountProducts($products, $percentage, $totalDiscountAmount, $items);
        [$percentage, $totalDiscountAmount] = $this->shippingDiscount($reward['type'], $percentage, $totalDiscountAmount);
        [$totalDiscountAmount, $couponInfo, $percentage] = $this->couponDiscount($reward['type'], $totalDiscountAmount, $coupon, $percentage);

        return [
            'percentage' => $percentage,
            'coupon' => $couponInfo,
            'amount' => $totalDiscountAmount,
            'items' => $items
        ];
    }

    /**
     * @param mixed $products
     * @param mixed $percentage
     * @param float|int $totalDiscountAmount
     * @param array $items
     * @return array
     */
    private function discountProducts(mixed $products, mixed $percentage, float|int $totalDiscountAmount, array $items): array
    {
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

        return array($percentage, $totalDiscountAmount, $items);
    }

    /**
     * @param $type
     * @param mixed $percentage
     * @param int $totalDiscountAmount
     * @return array
     */
    private function shippingDiscount($type, mixed $percentage, int $totalDiscountAmount): array
    {
        if ((int)$type === RewardsTypeOptions::SHIPPING_VALUE) {
            $shippingPercentage = $this->dinitosHelper->getRewardsShippingPercentage();
            $percentage = $shippingPercentage;
            $totalDiscountAmount = 0;
        }

        return array($percentage, $totalDiscountAmount);
    }

    /**
     * @param $type
     * @param int $totalDiscountAmount
     * @param mixed $coupon
     * @param int $percentage
     * @return array
     */
    private function couponDiscount($type, int $totalDiscountAmount, mixed $coupon, int $percentage): array
    {
        $couponInfo = '';
        if ((int)$type === RewardsTypeOptions::DISCOUNT_VALUE) {
            $totalDiscountAmount = 0;
            $couponInfo = [
                'id' => $coupon->getRuleId(),
                'coupon_code' => $coupon->__toArray()['coupon_code'],
                'from_date' => $coupon->getFromDate(),
                'to_date' => $coupon->getToDate(),
                'simple_action' => $coupon->getSimpleAction(),
                'discount_amount' => $coupon->getDiscountAmount(),
                'websites' => $coupon->getWebsiteIds()
            ];
            $percentage = 0;
        }

        return array($totalDiscountAmount, $couponInfo, $percentage);
    }
}