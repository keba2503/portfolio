<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Data
{
    const CODE = "blackfriday";

    const GROUP = "hiperdino_blackfriday";

    const SECTION = "general";
    const SECTION_PROMOTION = "promotions";

    const PROMO_END = "promo_end";
    const PROMO_START = "promo_start";
    const BLACKFRIDAY_TAG = "blackfriday_tag";
    const SHOW_TEXT_IN_CHECKOUT = "show_text_in_checkout";
    const LABEL_INFO_CHECKOUT = "label_info_checkout";
    const INFO_CHECKOUT_WITHOUT_DISCOUNT = "info_checkout_without_discount";
    const INFO_CHECKOUT_WITH_DISCOUNT = "info_checkout_with_discount";
    const INFO_CHECKOUT = "info_checkout";
    const EXIST_BLACKFRIDAY_DISCOUNT = "exist_blackfriday_discount";

    const HAS_ZBON_PROMOTION = "has_zbon_promotion";
    const ZBON_TEMPLATE_EMAIL = "zbon_template_email";

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Hiperdino\Promotions\Helper\Cart
     */
    private $_cartPromotionsHelper;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $_pricingHelper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Hiperdino\Promotions\Helper\Cart $cartPromotionsHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_cartPromotionsHelper = $cartPromotionsHelper;
        $this->_pricingHelper = $pricingHelper;
    }

    public function getBlackFridayExtraInfo($extraInfo, $amount, $remove = false)
    {
        $extraInfoArray = json_decode($extraInfo ?: "", true);

        if (!is_null($extraInfoArray)) {
            foreach ($extraInfoArray as $key => $arrayInfo) {
                if (isset($arrayInfo["code"]) && $arrayInfo["code"] == self::CODE) unset($extraInfoArray[$key]);
            }
        }

        if (is_null($extraInfoArray)) $extraInfoArray = [];

        if (!$remove) {
            array_push($extraInfoArray, $this->getBlackfridayArray($amount));
        }

        return json_encode($extraInfoArray);

    }

    public function getBlackFridayInfo($extraInfo)
    {
        $extraInfoArray = json_decode($extraInfo ?: "", true);

        $hasBlackFridayInfo = false;
        if (!is_null($extraInfoArray)) {
            foreach ($extraInfoArray as $key => $arrayInfo) {
                if (isset($arrayInfo["code"]) && $arrayInfo["code"] == self::CODE) $hasBlackFridayInfo = $extraInfoArray[$key];
            }
        }
        //TODO - Hacer los cambios para obtener los valores aduecuados de los descuentos con la frase adecuada
        //Creamos un hashBlackFridayInfo con los datos necesarios para presentar algo
        //$hasBlackFridayInfo = json_decode($this->getBlackFridayExtraInfo(), true);

        if (!$hasBlackFridayInfo) return false;
        if (!isset($hasBlackFridayInfo["info"])) return false;

        $blackFridayInfo = [];
        foreach ($hasBlackFridayInfo["info"] as $infoItem) {
            $blackFridayInfo[$infoItem["key"]] = $infoItem["value"];
        }

        return $blackFridayInfo;
    }


    private function getBlackfridayArray($amount)
    {
        $extraInfoArray = [
            "code" => self::CODE,
            "info" => [
                [
                    "key" => self::SHOW_TEXT_IN_CHECKOUT,
                    "value" => $this->getShowTextInCheckout()
                ],
                [
                    "key" => self::LABEL_INFO_CHECKOUT,
                    "value" => $this->getLabelInfoCheckout()
                ],
                [
                    "key" => self::EXIST_BLACKFRIDAY_DISCOUNT,
                    "value" => $amount ? 1 : 0
                ],
            ]
        ];


        if(!$amount) array_push($extraInfoArray["info"], [ "key" => self::INFO_CHECKOUT, "value" => $this->getInfoCheckoutWithoutDiscount()]);
        else array_push($extraInfoArray["info"], ["key" => self::INFO_CHECKOUT, "value" => __($this->getInfoCheckoutWithDiscount(), $this->_pricingHelper->currency($amount, true, false))]);

        return $extraInfoArray;
    }


    /**
     * Comprueba si la promoción está disponible
     * @return bool
     */
    public function promotionAvailable($date = 'now')
    {
        if($date = 'now'){
            $timezone = new \DateTimeZone('Atlantic/Canary');
            $today = new \DateTime($date, $timezone);
        }else{
            $today = new \DateTime($date);
        }

        $from_date = new \DateTime($this->getPromoStart()." 00:00:00");
        $to_date = new \DateTime($this->getPromoEnd()." 23:59:59");

        if ($from_date <= $today && $to_date >= $today) {
            return true;
        }

        return false;
    }


    /**
     * Recalcula los Rascas del Quote
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Exception
     * return int
     */
    public function recalculateDiscount($quote)
    {
        $extraInfo = $quote->getData("extra_info");

        $store = $quote->getStore();
        $isBlackFridayWebsite = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites', $store->getWebsiteId());

        if ($isBlackFridayWebsite && $this->promotionAvailable()) {

            $totalAmount = $this->_cartPromotionsHelper->getZBONDiscount($quote);

            if ($totalAmount) {
                $quote->setData("extra_info", $this->getBlackFridayExtraInfo($extraInfo, $totalAmount));
            } else {
                $quote->setData("extra_info", $this->getBlackFridayExtraInfo($extraInfo, 0));
            }

        } else {
            $quote->setData("extra_info", $this->getBlackFridayExtraInfo($extraInfo, 0, true));
        }

        $quote->save();
    }


    // GETTERS CONFIG VALUES

    private function getPromoStart()
    {
        return $this->getScopeConfigValue(self::PROMO_START);
    }

    private function getPromoEnd()
    {
        return $this->getScopeConfigValue(self::PROMO_END);
    }

    public function getBlackFridayTag()
    {
        return $this->getScopeConfigValue(self::BLACKFRIDAY_TAG);
    }

    public function getShowTextInCheckout()
    {
        return $this->getScopeConfigValue(self::SHOW_TEXT_IN_CHECKOUT);
    }

    public function getLabelInfoCheckout()
    {
        return $this->getScopeConfigValue(self::LABEL_INFO_CHECKOUT);
    }

    public function getInfoCheckoutWithoutDiscount()
    {
        return $this->getScopeConfigValue(self::INFO_CHECKOUT_WITHOUT_DISCOUNT);
    }

    public function getInfoCheckoutWithDiscount()
    {
        return $this->getScopeConfigValue(self::INFO_CHECKOUT_WITH_DISCOUNT);
    }

    public function getHasZBONPromotion()
    {
        return $this->getScopeConfigValue(self::HAS_ZBON_PROMOTION, self::SECTION_PROMOTION) ? true : false;
    }

    public function getZBONTemplateEmail()
    {
        return $this->getScopeConfigValue(self::ZBON_TEMPLATE_EMAIL, self::SECTION_PROMOTION);
    }

    private function getScopeConfigValue($field, $section = self::SECTION)
    {
        return $this->_scopeConfig->getValue(self::GROUP . "/" . $section . "/" . $field);
    }


}
