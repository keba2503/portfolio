<?php

namespace Hiperdino\Dinitos\Model\Services\Orders;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\SetDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetTypeMovements;
use Hiperdino\Dinitos\Model\Services\Rewards\GetQuote;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class used to register dinitos movements after payment.
 */
class AfterNewOrder
{
    const VALUE_ACCUMULATED = 0;
    const VALUE_REDEEMED = 1;

    public function __construct(
        protected QuoteRepository $quoteRepository,
        protected GetQuote $customerRewards,
        protected GetDinitos $customerDinitos,
        protected Logger $logger,
        protected GetTypeMovements $getTypeMovement,
        protected SetDinitos $setDinitosService,
    ) {
    }

    /**
     * The order of the movements has to be accumulation (if quote has dinitos) and then redemption in order
     * to be able to redeem the dinitos from the same quote
     *
     * @throws Exception
     */
    public function makeMovements($order): void
    {
        $quote = $this->quoteRepository->get($order->getQuoteId());
        if (!$quote) {
            throw new Exception("Ha habido un error recuperando el carrito para realizar los movimientos}");
        }
        $customerTotalDinitos = $this->customerDinitos->getCustomerDinitosTotal($quote->getCustomerId());
        $quoteTotalDinitos = $quote->getTotalDinitosQty();
        $rewards = $quote->getDinitosRewards() ? $this->customerRewards->getQuoteRewards($quote) : false;
        $movements = [];
        if ($quoteTotalDinitos || $rewards) {
            $movements[self::VALUE_ACCUMULATED] = ($quoteTotalDinitos > 0) ?
                ['type' => self::VALUE_ACCUMULATED, 'dinitos' => $quoteTotalDinitos] : [];
            $movements[self::VALUE_REDEEMED] = ($rewards) ?
                ['type' => self::VALUE_REDEEMED, 'dinitos' => $this->customerRewards->getQuoteRewardsDinitos($rewards)] : [];
        }
        foreach ($movements as $movement) {
            if (!empty($movement)) {
                $movementClass = $this->getTypeMovement->getMovement($movement['type']);
                $movementClass->handleMovement($quote, $movement['dinitos']);
            }
        }
        $spent = $rewards ? $this->customerRewards->getQuoteRewardsDinitos($rewards) : 0;
        $balance = $customerTotalDinitos + $quoteTotalDinitos - $spent;
        $this->setDinitosService->setCustomerDinitos($quote->getCustomerId(), $balance);
    }
}