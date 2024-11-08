<?php

namespace Hiperdino\BlackFriday\Block;

use Hiperdino\BlackFriday\Helper\BlackFriday2020;
use Hiperdino\BlackFriday\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Header extends Template {
	/**
	 * @var BlackFriday2020
	 */
	protected $blackFridayHelper;
	protected $config;

	public function __construct (
		Context $context,
		BlackFriday2020 $blackFridayHelper,
		Config $config,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->blackFridayHelper = $blackFridayHelper;
		$this->config = $config;
	}

	public function isOnBlackFridayStore() {
		return $this->blackFridayHelper->isBlackFridayStore();
	}

	public function canAccessBlackFriday() {
		return $this->blackFridayHelper->canAccessBlackFriday();
	}

	public function getLoginUrl() {
		return $this->getUrl('hiperdino_blackfriday/index/login');
	}

	public function getLogoutUrl() {
		return $this->getUrl('hiperdino_blackfriday/index/logout');
	}

	public function getStoresJson() {
		return $this->config->getStoresJson();
	}

	public function getGoogleMapsKey() {
		return $this->_scopeConfig->getValue('tiendas/keys/maps', ScopeInterface::SCOPE_STORE);
	}

    public function isLoggedIn()
    {
        return $this->blackFridayHelper->isLoggedIn();
    }

    public function getStoresJsonSortedByCity()
    {
        $sortedArray = [];
        $json = $this->getStoresJson() ?: "";
        $dataArray = json_decode($json, true);
        foreach($dataArray as $island => $cities) {
            ksort($cities);
            $sortedArray[$island] = $cities;
        }
        return json_encode($sortedArray, JSON_UNESCAPED_UNICODE);
    }
}
