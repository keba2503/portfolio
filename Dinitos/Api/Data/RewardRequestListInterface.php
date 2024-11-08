<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RewardRequestListInterface extends CustomAttributesDataInterface
{
    const ITEMS = "items";

    /**
     * @return \Hiperdino\Dinitos\Api\Data\RewardResponseInterface[]
     */
    public function getItems();

    /**
     * @param \Hiperdino\Dinitos\Api\Data\RewardResponseInterface[] $items
     * @return $this
     */
    public function setItems($items);

}