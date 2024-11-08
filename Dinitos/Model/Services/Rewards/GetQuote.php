<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\CollectionFactory;
use Hiperdino\Dinitos\Model\Rewards\DiscountManager;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Quote\Model\Quote as QuoteModel;
use Magento\Quote\Model\Quote as MagentoQuote;
use Magento\Quote\Model\QuoteRepository;
use Singular\EcommerceApp\Helper\Cart;

class GetQuote
{
    public function __construct(
        protected Logger $logger,
        protected QuoteRepository $quoteRepository,
        protected Cart $cartHelper,
        protected CollectionFactory $rewardCollection,
        protected GetDinitos $customerDinitos,
        protected Config $config,
        protected DiscountManager $rewardManager
    ) {
    }

    public function getQuoteRewards($quote)
    {
        return ($quote->getDinitosRewards() && !is_null($quote->getDinitosRewards())) ? json_decode($quote->getDinitosRewards(), associative: true) : [];
    }

    public function getQuoteRewardsDinitos($rewardsArray)
    {
        $total = 0;
        foreach ($rewardsArray as $reward) {
            $total += $reward['dinitos_qty'];
        }

        return $total;
    }

    /**
     * @throws Exception
     */
    public function setRewardsToQuote($rewards, $quote = null)
    {
        try {
            $quote = $this->getValidQuote($quote);
            $rewardsIds = $this->getRewardsIds($rewards);
            $rewards = $this->getRewardsById($rewardsIds);

            $this->applyRewardsToQuote($quote, $rewards);

            return true;
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @throws Exception
     */
    private function getValidQuote($quote)
    {
        if (!$quote) {
            $quote = $this->cartHelper->getQuote();
        }
        if (!$quote) {
            throw new Exception(__("Ha habido un error seleccionando las recompensas."));
        }

        return $quote;
    }

    private function getRewardsIds($rewards)
    {
        return !empty($rewards) ? $this->formatRewardsArray($rewards) : [];
    }

    /**
     * @throws Exception
     */
    private function applyRewardsToQuote($quote, $rewards)
    {
        $customerDinitos = $this->customerDinitos->getCustomerDinitosTotal($quote->getCustomerId());
        $totalRewardsDinitos = $this->getQuoteRewardsDinitos($rewards);
        $totalQuoteDinitos = $quote->getTotalDinitosQty();
        
        if (($customerDinitos + $totalQuoteDinitos) < $totalRewardsDinitos) {
            throw new Exception(__("Las recompensas seleccionadas exceden los dinitos del cliente."));
        }

        $quote->setData('dinitos_rewards', $this->encodeRewards($rewards));
        $this->quoteRepository->save($quote);
    }


    /**
     * @throws Exception
     */
    private function handleException($e)
    {
        $this->logger->logDinitosService(__("Error guardando las recompensas del customer en el carrito\n {$e->getMessage()}"));
        throw new Exception($e->getMessage());
    }

    private function formatRewardsArray($rewards)
    {
        return array_map(function ($reward) {
            return $reward['id'];
        }, $rewards);
    }

    public function unsetCustomerRewardsFromQuote(QuoteModel|MagentoQuote $quote = null): QuoteModel|bool
    {
        try {
            if (!$quote) {
                $quote = $this->cartHelper->getQuote();
            }
            $this->rewardManager->removeCoupon();
            $quote->setData('dinitos_rewards');
            $this->quoteRepository->save($quote);

            return $quote;
        } catch (Exception $e) {
            $this->logger->logDinitosReward(__("Error eliminando las recompensas del carrito:\n {$e->getMessage()}"));

            return false;
        }
    }

    private function getRewardsById($rewardIds): array
    {
        $rewards = [];
        try {
            $rewards = $this->rewardCollection->create()->addFieldToFilter('id', ['in' => $rewardIds])->getItems();
        } catch (Exception $e) {
            $this->logger->logDinitosReward(__("Error obteniendo las recompensas: \n {$e->getMessage()}"));
        }

        return $rewards;
    }

    private function encodeRewards($rewards): string
    {
        $encodedRewards = [];
        foreach ($rewards as $key => $reward) {
            $encodedRewards[$key] = $reward->getData();
        }

        return json_encode($encodedRewards) ?: "";
    }
}