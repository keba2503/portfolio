<?php

namespace Hiperdino\BlackFriday\Plugin;

use Hiperdino\BlackFriday\Helper\BlackFriday2020 as BlackFridayHelper;
use Hiperdino\FreeNavigation\Helper\Cookies as CookieHelper;
use Magento\Customer\Model\Session as CustomerSession;

class BuyingFor {
	protected $blackFridayHelper;
	protected $cookieHelper;
	protected $customerSession;

	public function __construct(
		BlackFridayHelper $blackFridayHelper,
		CookieHelper $cookieHelper,
		CustomerSession $customerSession
	) {
		$this->blackFridayHelper = $blackFridayHelper;
		$this->cookieHelper = $cookieHelper;
		$this->customerSession = $customerSession;
	}

	/**
	 * @param \Hiperdino\FreeNavigation\Helper\BuyingFor $subject
	 * @param $procede
	 * @return mixed
	 */
	public function aroundCanShowTopbarBuying(\Hiperdino\FreeNavigation\Helper\BuyingFor $subject, $procede) {
		if ($this->blackFridayHelper->isBlackFridayStore()) {
			return false;
		}

		return $procede();
	}

	public function aroundSetBuyingForPostcode(\Hiperdino\FreeNavigation\Helper\BuyingFor $subject, $procede, $postcode) {
		$procede($postcode);
		if (($realPostcode = $this->cookieHelper->getCookie(BlackFridayHelper::BLACK_FRIDAY_POSTCODE_COOKIE)) && $this->customerSession->isLoggedIn()) {
			$this->blackFridayHelper->setRealPostcode($realPostcode);
		}
	}
}
