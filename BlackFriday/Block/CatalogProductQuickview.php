<?php

namespace Hiperdino\BlackFriday\Block;

use Magento\Framework\View\Element\Template;

class CatalogProductQuickview extends Template
{

    /**
     * @return bool
     */
    public function showAdvice()
    {
        $isBlackFridayStore = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
        if(! $isBlackFridayStore) return false;
        return (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website_messages/show_availability_msg');
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return (string) $this->_scopeConfig->getValue('hiperdino_blackfriday/website_messages/availability_msg');
    }
}
