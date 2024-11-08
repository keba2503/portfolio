<?php

namespace Hiperdino\Anniversary2020\Plugin;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Homai\Helper\Coupon;

class AfterCustomerGetCoupons
{
    protected Config $anniversaryConfig;

    public function __construct(
        Config $anniversaryConfig
    ) {
        $this->anniversaryConfig = $anniversaryConfig;
    }

    public function afterGetCustomerCoupons(Coupon $subject, $result)
    {
        foreach ($result as $key => $coupon) {
            if (in_array($coupon['promotionId'], $this->anniversaryConfig->getHomaiPromotionIds())) {
                $result[$key]['balance'] = $this->anniversaryConfig->getHomaiPrizeText();
            }
        }

        return $result;
    }
}
