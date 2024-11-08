<?php

namespace Hiperdino\BlackFriday\Block\Cart\Totals;

class BlackFriday extends \Magento\Checkout\Block\Cart
{

    /**
     * @var \Hiperdino\BlackFriday\Helper\Data
     */
    private $_blackFridayHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Hiperdino\BlackFriday\Helper\Data $blackFridayHelper,
        array $data = []
    )
    {
        parent::__construct($context, $customerSession, $checkoutSession, $catalogUrlBuilder, $cartHelper, $httpContext, $data);
        $this->_blackFridayHelper = $blackFridayHelper;
    }

    /**
     * @return bool
     */
    public function showBlackFridayInfo(){
        $isBlackFridayStore = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
        if(! $isBlackFridayStore) return false;
        return $this->_blackFridayHelper->promotionAvailable() && $this->_blackFridayHelper->getShowTextInCheckout();
    }

    public function getBlackFridayInfo(){
        return  $this->_blackFridayHelper->getBlackFridayInfo($this->getQuote()->getData("extra_info"));
    }

    /**
     * @return string
     */
    public function getAvailabilityMessage()
    {
        $isBlackFridayStore = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
        if(! $isBlackFridayStore) return '';
        $canShow = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website_messages/show_availability_msg');
        if($canShow) {
            return (string) $this->_scopeConfig->getValue('hiperdino_blackfriday/website_messages/availability_msg');
        } else {
            return '';
        }
    }

}
