<?php

namespace Hiperdino\BlackFriday\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;

class TiendasAfterGetPickupShop
{

    protected $_scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterGetPickupShops($subject, $result)
    {
        $isBlackFridayStore = (bool) $this->_scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
        if($isBlackFridayStore) {
            $result->addFieldToFilter('is_black_friday', '1');
        } else {
            $result->addFieldToFilter('is_black_friday', [
                ['null' => true],
                ['eq' => '0']
            ]);
        }
        return $result;
    }
}
