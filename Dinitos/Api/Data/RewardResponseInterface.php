<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RewardResponseInterface extends CustomAttributesDataInterface
{
    const REWARD_ID = 'reward_id';

    /**
     * @return string
     */
    public function getRewardId();

    /**
     * @param $rewardId
     * @return \Hiperdino\Dinitos\Api\Data\RewardResponseInterface
     */
    public function setRewardId($rewardId);
}