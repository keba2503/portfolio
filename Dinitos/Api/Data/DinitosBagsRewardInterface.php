<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface DinitosBagsRewardInterface extends CustomAttributesDataInterface
{
    const DISCOUNT_AMOUNT = 'discount_amount';

    /**
     * @return string
     */
    public function getDiscountAmount();

    /**
     * @param string $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount);
}