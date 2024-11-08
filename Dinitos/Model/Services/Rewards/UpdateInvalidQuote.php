<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Magento\Quote\Model\QuoteRepository;
use Singular\EcommerceApp\Helper\Cart as CartHelper;

class UpdateInvalidQuote
{
    public function __construct(
        protected Logger $logger,
        protected CartHelper $cartHelper,
        protected QuoteRepository $quoteRepository
    ) {
    }

    public function updateQuoteRewards($availableRewards, $quoteParams = null)
    {
        try {
            $quote = $this->getQuoteIfNotProvided($quoteParams);
            $quoteRewards = $this->getQuoteRewardsArray($quote);
            $resultRewards = $this->filterAvailableRewards($quoteRewards, $availableRewards);
            if ($this->hasRewardsChanged($resultRewards, $quoteRewards)) {
                $this->updateQuoteWithRewards($quote, $resultRewards);
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }

        return $availableRewards;
    }

    public function hasRewardsChanged($resultRewards, $quoteRewards): bool
    {
        if (empty($quoteRewards) && empty($resultRewards)) {
            return false;
        }

        return json_encode($quoteRewards) !== json_encode($resultRewards);
    }

    private function getQuoteIfNotProvided($quote)
    {
        if (!$quote) {
            $quote = $this->cartHelper->getQuote();
        }
        if (!$quote) {
            $this->logger->logDinitosReward(__("Error al recuperar el carrito"));
        }

        return $quote;
    }

    private function getQuoteRewardsArray($quote)
    {
        $quoteRewards = $quote->getDinitosRewards() ? json_decode($quote->getDinitosRewards(), true) : null;

        return $quoteRewards ?? [];
    }

    private function filterAvailableRewards($quoteRewards, $availableRewards)
    {
        $resultRewardsArray = [];
        foreach ($quoteRewards as $quoteReward) {
            if (in_array($quoteReward, $this->availableRewardsToArray($availableRewards))) {
                $resultRewardsArray[$quoteReward['id']] = $quoteReward;
            };
        };

        return $resultRewardsArray;
    }

    private function updateQuoteWithRewards($quote, $resultRewardsArray)
    {
        try {
            empty($resultRewardsArray) ? $quote->setData('dinitos_rewards') : $quote->setData('dinitos_rewards', json_encode($resultRewardsArray));
            $this->quoteRepository->save($quote);
        } catch (Exception $e) {
            $this->logger->logDinitosService(__("Error eliminando las recompensas no disponibles del carrito: \n {$e->getMessage()}"));
        }
    }

    private function handleException($e)
    {
        $this->logger->logDinitosService(__("Error actualizando las recompensas del carrito: \n {$e->getMessage()}"));
    }

    private function availableRewardsToArray($availableRewards): array
    {
        $rewardsArray = [];
        if (!empty($availableRewards)) {
            foreach ($availableRewards as $availableReward) {
                $rewardsArray[] = $availableReward->getData();
            }
        }

        return $rewardsArray;
    }
}