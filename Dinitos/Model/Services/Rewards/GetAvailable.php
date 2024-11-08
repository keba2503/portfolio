<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Hiperdino\Dinitos\Api\Data\RewardInterface;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\CollectionFactory;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Singular\EcommerceApp\Helper\Cart as CartHelper;

class GetAvailable
{
    const REWARD_FLATRATE_VALUE = 'Envío a domicilio';
    const REWARD_STORE_PICKUP_VALUE = 'Recogida en tienda';
    const REWARD_LOCKER_VALUE = 'Taquillas';
    const REWARD_PICKUP_POINT_VALUE = 'Punto de recogida';
    const SHIPPING_FREE_COST = 0;
    const SHIPPING_FREE_INDICATOR = 'dinitos_acumulated';

    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected CollectionFactory $rewardCollectionFactory,
        protected CartHelper $cartHelper,
        protected Logger $logger,
        protected UpdateInvalidQuote $updateInvalidQuoteRewards,
        protected Config $configHelper
    ) {
    }

    public function getStoreViewRewards($step = null): array
    {
        try {
            $quote = $this->cartHelper->getQuote();
            if (!$quote) {
                throw new Exception("Error cargando el carrito");
            }
            $storeViewId = $this->getCurrentStoreView();
            $rewards = $this->rewardCollectionFactory->create()->addFieldToFilter('active', 1)->addStoreFilter($storeViewId)->getItems();
            if ($step === '0') {
                return $rewards;
            }
            $shippingFreeIndicator = $quote->getData('free_shipping_method_indicator') == self::SHIPPING_FREE_INDICATOR;
            $filteredRewards = [];
            $filteredRewards = $this->getRewards($rewards, $quote, $shippingFreeIndicator, $filteredRewards);
            $this->updateInvalidQuoteRewards->updateQuoteRewards($filteredRewards, $quote);
        } catch (Exception $e) {
            $this->logger->logDinitosReward("Error obteniendo las recompensas: \n {$e->getMessage()}");

            return [];
        }

        return $filteredRewards;
    }

    /**
     * @param Quote $quote
     * @param RewardInterface $reward
     * @param bool $shippingFreeIndicator
     * @param array $filteredRewards
     * @return array
     */
    private function filterRewards(Quote $quote, RewardInterface $reward, bool $shippingFreeIndicator, array $filteredRewards): array
    {
        $isFree = $quote->getShippingAddress()->getShippingAmount() == self::SHIPPING_FREE_COST;
        $isDiscountSet = $this->configHelper->getRewardsShippingPercentage();
        $isSameMethod = $this->mapRewardToShippingMethod($reward->getEntityIdentifier()) == $this->getShippingMethod($quote);
        if ($isSameMethod && $isDiscountSet && (!$isFree || $shippingFreeIndicator)) {
            $filteredRewards[] = $reward;
        }

        return $filteredRewards;
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getCurrentStoreView(): int
    {
        return $this->storeManager->getStore()->getStoreId();
    }

    private function mapRewardToShippingMethod($rewardShippingMethod)
    {
        switch ($rewardShippingMethod) {
            case $rewardShippingMethod == self::REWARD_FLATRATE_VALUE:
                return 'flatrate_flatrate';
            case $rewardShippingMethod == self::REWARD_LOCKER_VALUE:
                return 'taquillas_taquillas';
            case $rewardShippingMethod == self::REWARD_PICKUP_POINT_VALUE:
                return 'pickuppoint_pickuppoint';
            case $rewardShippingMethod == self::REWARD_STORE_PICKUP_VALUE:
                return 'pickup_pickup';
        }
    }

    private function getIsBag($product): bool
    {
        $bagsArray = explode(',', $product);
        $bagSKUs = $this->configHelper->getBagSkus();
        $isBag = false;
        foreach ($bagsArray as $bag) {
            if (in_array($bag, $bagSKUs)) {
                $isBag = true;
            }
        }

        return $isBag;
    }

    private function getShippingMethod(Quote $quote)
    {
        return $quote->getShippingAddress()->getShippingMethod();
    }

    /**
     * @param $rewards
     * @param Quote $quote
     * @param bool $shippingFreeIndicator
     * @param mixed $filteredRewards
     * @return array|mixed
     */
    private function getRewards($rewards, Quote $quote, bool $shippingFreeIndicator, mixed $filteredRewards): mixed
    {
        if (!empty($rewards)) {
            foreach ($rewards as $reward) {
                /* @var RewardInterface $reward */
                if ($reward->getType() == RewardsTypeOptions::SHIPPING_VALUE) {
                    $filteredRewards = $this->filterRewards($quote, $reward, $shippingFreeIndicator, $filteredRewards);
                } elseif ($reward->getType() == RewardsTypeOptions::PRODUCT_VALUE && !$this->getIsBag($reward->getEntityIdentifier())) {
                    continue;
                } else {
                    $filteredRewards[] = $reward;
                }
            }
        }

        return $filteredRewards;
    }
}