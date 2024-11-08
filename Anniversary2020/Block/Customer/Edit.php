<?php

namespace Hiperdino\Anniversary2020\Block\Customer;

use Hiperdino\Anniversary2020\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Singular\Islands\Model\Attribute\Source\Islands;

class Edit extends Template
{
    protected Session $customerSession;
    protected Islands $islandsSource;
    protected Config $helperConfig;

    public function __construct(
        Islands $islandsSource,
        Session $customerSession,
        Template\Context $context,
        Config $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->islandsSource = $islandsSource;
        $this->helperConfig = $helperConfig;
    }

    /**
     * @return string
     */
    public function getCustomerDni()
    {
        $customer = $this->customerSession->getCustomer();

        return $customer->getTaxvat();
    }

    public function getCustomerTelephone()
    {
        $customer = $this->customerSession->getCustomer();

        return (string)$customer->getData('customer_telephone');
    }

    public function getCustomerIsland()
    {
        $customer = $this->customerSession->getCustomer();

        return (int)$customer->getData('customer_island');
    }

    /**
     * @return array
     */
    public function getIslandsOptions()
    {
        return $this->islandsSource->getAllOptions();
    }

    /**
     * @return string
     */
    public function getAjaxActionUrl()
    {
        return $this->getUrl('hdanniversary/ajax/customerEditPost');
    }

    /**
     * @return string
     */
    public function getTermsAcceptText(): string
    {
        $baseText = $this->helperConfig->getTextTermsPromotion();
        $baseLink1 = $this->helperConfig->getUrlPageTermsPromotion1();
        $url1 = $this->_urlBuilder->getUrl($baseLink1);

        return str_replace('%1', $url1, $baseText);
    }

    /**
     * @return string
     */
    public function getTermsAcceptCheckboxText(): string
    {
        $baseText = $this->helperConfig->getTermsAccept();

        $baseLink1 = $this->helperConfig->getUrlPageTermsPromotion1();
        $url1 = $this->_urlBuilder->getUrl($baseLink1);

        $baseLink2 = $this->helperConfig->getUrlPageTermsPromotion2();
        $url2 = $this->_urlBuilder->getUrl($baseLink2);

        return str_replace(['%1', '%2'], [$url1, $url2], $baseText);
    }
}
