<?php

namespace Hiperdino\Dinitos\Model\Rewards;

use Exception;
use Hiperdino\Checkout\Helper\MagentoCoupon;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\Rewards\GetCouponCodes;
use Singular\EcommerceApp\Helper\Cart;

class DiscountManager
{

    public function __construct(
        protected MagentoCoupon $magentoCouponHelper,
        protected Logger $logger,
        protected Cart $cartHelper,
        protected GetCouponCodes $discountRewardsCodes
    ) {
    }

    /**
     * @throws Exception
     */
    public function applyCoupon($discountReward)
    {
        $result = ['success' => false];
        $quote = $this->cartHelper->getQuote();
        if (!$quote) {
            return ['success' => false, 'message' => "No se ha podido recuperar el carrito."];
        }
        try {
            $couponCode = $discountReward['coupon_code'];
            $result = ($couponCode != $quote->getCouponCode()) ?
                $this->magentoCouponHelper->applyMagentoCoupon($couponCode) :
                $this->removeCoupon();
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error aplicando el cupón de la recompensa:\n {$e->getMessage()}");

            return $result;
        }

        return $result;
    }

    public function removeCoupon()
    {
        $result = ['success' => false];
        $quote = $this->cartHelper->getQuote();
        if (!$quote) {
            return ['success' => false, 'message' => "No se ha podido recuperar el carrito."];
        }
        try {
            if (!$quote->getCouponCode()) {
                return ['success' => true];
            }
            if ($this->isDiscountRewardCode($quote->getCouponCode())) {
                return $this->magentoCouponHelper->applyMagentoCoupon("");
            }
        } catch (Exception $e) {
            $this->logger->logDinitosService("Error aplicando el cupón de la recompensa:\n {$e->getMessage()}");
        }

        return $result;
    }

    private function isDiscountRewardCode($code): bool
    {
        $codes = $this->discountRewardsCodes->getAllCodes();

        return in_array($code, $codes);
    }

}