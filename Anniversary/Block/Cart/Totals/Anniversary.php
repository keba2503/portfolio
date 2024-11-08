<?php

namespace Hiperdino\Anniversary\Block\Cart\Totals;

use Hiperdino\Anniversary\Helper\Config;
use Hiperdino\Anniversary\Helper\ExtraInfo;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Block\Cart;
use Magento\Checkout\Helper\Cart as HelperCart;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;

class Anniversary extends Cart
{
    protected HelperCart $cartHelper;
    protected ExtraInfo $extraInfo;
    protected Config $config;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        Config $config,
        Session $checkoutSession,
        Url $catalogUrlBuilder,
        HelperCart $cartHelper,
        HttpContext $httpContext,
        ExtraInfo $extraInfoManager,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $catalogUrlBuilder, $cartHelper, $httpContext, $data);
        $this->cartHelper = $cartHelper;
        $this->extraInfo = $extraInfoManager;
        $this->config = $config;
    }

    public function showAnniversaryInfo()
    {
        return $this->config->promotionAvailable();
    }

    public function getAnniversaryInfo()
    {
        return $this->extraInfo->getAnniversaryInfo($this->getQuote()->getData("extra_info"));
    }
}
