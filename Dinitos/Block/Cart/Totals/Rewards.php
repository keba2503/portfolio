<?php

namespace Hiperdino\Dinitos\Block\Cart\Totals;

use Hiperdino\Dinitos\Helper\Config;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Block\Cart;
use Magento\Checkout\Helper\Cart as HelperCart;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;
use Singular\EcommerceApp\Helper\Cart as ExtraInfo;
use Magento\Framework\Pricing\Helper\Data;

class Rewards extends Cart
{
    const PRICE_FREE = 0;

    public function __construct(
        protected Context $context,
        protected CustomerSession $customerSession,
        protected Session $checkoutSession,
        protected Url $catalogUrlBuilder,
        protected HelperCart $cartHelper,
        HttpContext $httpContext,
        protected Config $config,
        protected ExtraInfo $extraInfo,
        protected Data $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $catalogUrlBuilder, $cartHelper, $httpContext, $data);
    }

    public function getDinitosRewardsFromQuote()
    {
        $dataFromTable = $this->extraInfo->getQuote()->getData("dinitos_rewards");

        return $dataFromTable ? json_decode($dataFromTable, true) : [];
    }

    public function getRewardsProducTypeFromQuote()
    {
        $dataFromTable = $this->extraInfo->getQuote()->getData("dinitos_rewards");
        $decodedData = $dataFromTable ? json_decode($dataFromTable, true) : [];

        return array_filter($decodedData, function ($element) {
            return isset($element['type']) && $element['type'] === '0';
        });
    }

    public function getRewardsProductsPercentage()
    {
        return $this->config->getRewardsProductsPercentage();
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    public function dinitosEnabled()
    {
        return $this->config->isDinitosAccumulatedEnabledWeb();
    }
}


