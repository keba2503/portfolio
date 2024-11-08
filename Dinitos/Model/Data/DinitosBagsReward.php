<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\DinitosBagsRewardInterface;
use Magento\Framework\Api\AbstractExtensibleObject as AbstractExtensibleObject;

class DinitosBagsReward extends AbstractExtensibleObject implements DinitosBagsRewardInterface
{
    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->_get(self::DISCOUNT_AMOUNT);
    }

    /**
     * @param string $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }
}