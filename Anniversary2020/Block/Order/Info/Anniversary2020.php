<?php

namespace Hiperdino\Anniversary2020\Block\Order\Info;

use Hiperdino\Anniversary2020\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Anniversary2020 extends Template
{
    protected Config $helperConfig;

    /**
     * @param Context $context
     * @param Config $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $helperConfig,
        array $data = []
    ) {
        $this->helperConfig = $helperConfig;
        parent::__construct($context, $data);
    }

    public function isAnniversaryEnabled()
    {
        return $this->helperConfig->isAnniversaryEnabled();
    }

    public function getTitle()
    {
        return $this->helperConfig->getRegisterRascaTitle();
    }

    public function getTextPromo()
    {
        return $this->helperConfig->getTextEmailPromotion();
    }

    public function getURLPromo()
    {
        return $this->helperConfig->getUrlEmailPromotion();
    }
}
