<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\RewardResponseInterface;
use Magento\Framework\Model\AbstractModel;

class RewardResponse extends AbstractModel implements RewardResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getRewardId()
    {
        return $this->getData(self::REWARD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRewardId($rewardId): RewardResponseInterface
    {
        return $this->setData(self::REWARD_ID, $rewardId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttribute($attributeCode)
    {
        // TODO: Implement getCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: Implement setCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttributes()
    {
        // TODO: Implement getCustomAttributes() method.
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttributes(array $attributes)
    {
        // TODO: Implement setCustomAttributes() method.
    }
}