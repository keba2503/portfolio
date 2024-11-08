<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\RewardRequestListInterface;
use Magento\Framework\Model\AbstractModel;

class RewardRequestList extends AbstractModel implements RewardRequestListInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
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