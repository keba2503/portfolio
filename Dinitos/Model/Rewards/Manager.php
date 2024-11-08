<?php

namespace Hiperdino\Dinitos\Model\Rewards;

use Exception;
use Hiperdino\Dinitos\Api\Data\RewardInterface;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\CollectionFactory;
use Hiperdino\Dinitos\Model\Services\Rewards\GetQuote;
use Hiperdino\Dinitos\Model\Services\Shipping\UpdateFreeShippingIndicator;
use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Singular\EcommerceApp\Helper\Cart;

class Manager
{

    public function __construct(
        protected DiscountManager $discountReward,
        protected GetQuote $quoteRewards,
        protected Cart $cartHelper,
        protected Logger $logger,
        protected CollectionFactory $collectionFactory,
        protected RewardRepository $rewardRepository,
        protected UpdateFreeShippingIndicator $updateFreeShippingIndicator,
        protected $rewards = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function manageRewards($reward)
    {
        try {
            $this->setQuoteRewards($this->getQuoteDinitosRewards());
            $rewardEntity = $this->rewardRepository->getById($reward['id']);
            $isInRewardArray = $this->checkQuoteRewards($rewardEntity);

            $this->updateRewardToQuote($isInRewardArray, $rewardEntity, $reward['type']);

            empty($this->rewards) ?
                $this->quoteRewards->unsetCustomerRewardsFromQuote() :
                $this->quoteRewards->setRewardsToQuote($this->rewards);
        } catch (Exception $e) {
            if ($reward['type'] == RewardsTypeOptions::DISCOUNT_VALUE) {
                $this->discountReward->removeCoupon();
            }
            $this->logger->logDinitosService("Error en manageRewards: \n {$e->getMessage()}");
            throw new Exception($e->getMessage());
        }
    }

    public function manageAppRewards($rewardsIds)
    {
        try {
            $unsetRewards = $this->quoteRewards->unsetCustomerRewardsFromQuote();
            $this->updateFreeShippingIndicator->execute();

            if (!$rewardsIds) {

                return $unsetRewards ?
                    ['success' => true, 'message' => "Recompensas eliminadas."] :
                    ['success' => false, 'message' => "Error eliminando las recompensas del usuario."];
            }
            $rewards = $this->getRewardsById($rewardsIds);
            foreach ($rewards as $reward) {
                $this->addRewardToArray($reward);
            }
            $this->quoteRewards->setRewardsToQuote($this->rewards);

            return ['success' => true, 'message' => "Recompensas añadidas."];
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error en AppRewardManager: \n {$e->getMessage()}");

            return ['success' => false, 'message' => "Ha habido un error añadiendo las recompensas: {$e->getMessage()}"];
        }
    }

    /**
     * @throws Exception
     */
    private function getQuoteDinitosRewards()
    {
        try {
            $quote = $this->cartHelper->getQuote();
            if (!$quote) {
                throw new Exception("Error recuperando el carrito.");
            }
            $this->rewards = ($quote->getDinitosRewards() && !empty($quote->getDinitosRewards())) ? json_decode($quote->getDinitosRewards(), associative: true) : [];

            return $this->rewards;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function checkQuoteRewards($selectedReward): bool
    {
        try {
            $quoteRewards = $this->getQuoteRewards();
            if (!$quoteRewards) {
                return false;
            }
            foreach ($quoteRewards as $quoteReward) {
                if ($quoteReward['id'] == $selectedReward['id']) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function addRewardToArray($selectedReward)
    {
        try {
            $quoteRewards = $this->getQuoteRewards();
            if ($this->getIsDiscountReward($selectedReward)) {
                $selectedReward->setData('coupon_code', $selectedReward->getEntityIdentifier());
                $result = $this->applyDiscountCoupon($selectedReward);
                if ($result['success']) {
                    $rewardsArray = array_merge($quoteRewards, [$selectedReward]);
                    $this->setQuoteRewards($rewardsArray);

                    return true;
                }

                return false;
            }
            $rewardsArray = array_merge($quoteRewards, [$selectedReward]);
            $this->setQuoteRewards($rewardsArray);

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function removeRewardFromArray($selectedReward)
    {
        try {
            $quoteRewards = $this->getQuoteRewards();
            $filteredRewards = array_filter($quoteRewards, function ($reward) use ($selectedReward) {
                if ($reward['id'] != $selectedReward['id']) {
                    return $reward;
                }
            });
            if ($this->getIsDiscountReward($selectedReward)) {
                $result = $this->discountReward->removeCoupon();
                if ($result['success']) {
                    $this->setQuoteRewards($filteredRewards);

                    return true;
                }

                return false;
            }
            $this->setQuoteRewards($filteredRewards);

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function getIsDiscountReward($selectedReward): bool
    {
        try {
            return $selectedReward['type'] == RewardsTypeOptions::DISCOUNT_VALUE;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function getQuoteRewards(): array
    {
        return $this->rewards ?: [];
    }

    private function setQuoteRewards($quoteRewards)
    {
        return $this->rewards = $quoteRewards;
    }

    /**
     * @throws Exception
     */
    private function applyDiscountCoupon($discountReward)
    {
        try {
            return $this->discountReward->applyCoupon($discountReward);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function getRewardsById($rewardIds)
    {
        return $this->collectionFactory->create()->addFieldToFilter('id', ['in', $rewardIds])->getItems();
    }

    /**
     * @param bool $isInRewardArray
     * @param RewardInterface $rewardEntity
     * @param $type
     * @return void
     * @throws Exception
     */
    public function updateRewardToQuote(bool $isInRewardArray, RewardInterface $rewardEntity, $type): void
    {
        if ($isInRewardArray) {
            $this->removeRewardFromArray($rewardEntity);

            if ($type == RewardsTypeOptions::SHIPPING_VALUE) {
                $this->updateFreeShippingIndicator->execute();
            }
        } else {
            $this->addRewardToArray($rewardEntity);
        }
    }
}