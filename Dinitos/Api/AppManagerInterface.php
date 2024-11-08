<?php

namespace Hiperdino\Dinitos\Api;

/**
 * @api
 */
interface AppManagerInterface
{
    /**
     * @param int $customerId
     * @return \Hiperdino\Dinitos\Api\Data\CustomerDinitosResponseInterface;
     */
    public function getCustomerDinitos($customerId);

    /**
     * @param int $customerId
     * @return \Hiperdino\Dinitos\Api\Data\HistoryListInterface
     */
    public function getCustomerDinitosHistory($customerId): Data\HistoryListInterface;

    /**
     * @param int $customerId
     * @return \Hiperdino\Dinitos\Api\Data\RewardListInterface
     */
    public function getStoreViewRewards($customerId): Data\RewardListInterface;

    /**
     * @param int $customerId
     * @return \Hiperdino\Dinitos\Api\Data\PackageListResponseInterface
     */
    public function getCustomerClosestExpiredPackage($customerId);

    /**
     * @param int $customerId
     * @return \Hiperdino\Dinitos\Api\Data\CustomerDinitosResponseInterface;
     */
    public function getCustomerDinitosPlusQuote($customerId);
}