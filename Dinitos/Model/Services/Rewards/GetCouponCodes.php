<?php

namespace Hiperdino\Dinitos\Model\Services\Rewards;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\Collection;

class GetCouponCodes
{

    public function __construct(
        protected Collection $collection,
        protected Logger $logger
    ) {
    }

    public function getAllCodes()
    {
        try {
            $discountsRewards = $this->getDiscountRewards();

            return $this->mapToArray($discountsRewards);
        } catch (Exception $e) {
            $this->logger->logDinitosService(__("Error en getAllCodes: {$e->getMessage()}"));
        }
    }

    private function getDiscountRewards()
    {
        return $this->collection->addDiscountTypeFilter()->getItems();
    }

    private function mapToArray($discountsRewards)
    {
        $codes = [];
        foreach ($discountsRewards as $discountsReward) {
            $codes[] = $discountsReward->getEntityIdentifier();
        }

        return $codes;
    }
}