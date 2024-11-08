<?php

namespace Hiperdino\BlackFriday\Helper;

use Hiperdino\FreeNavigation\Helper\BuyingFor;
use Hiperdino\FreeNavigation\Helper\Cookies;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Singular\Tiendas\Model\TiendasFactory;

class BlackFriday2020 {
	const BLACK_FRIDAY_POSTCODE_COOKIE = 'bf_rcp';

	/**
	 * @var ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @var Config
	 */
	protected $blackFridayConfig;

	/**
	 * @var StoreRepositoryInterface
	 */
	protected $storeRepository;

	protected $customerSession;
	protected $customerRepository;
	protected $cookieHelper;
	protected $buyingforHelper;
	protected $dataHelper;
	protected $tiendasFactory;

	public function __construct(
		ScopeConfigInterface $scopeConfig,
		Config $blackFridayConfig,
		Data $dataHelper,
		Session $customerSession,
		CustomerRepositoryInterface $customerRepository,
		Cookies $cookieHelper,
		BuyingFor $buyingforHelper,
		StoreRepositoryInterface $storeRepository,
        TiendasFactory $tiendasFactory
	) {
		$this->scopeConfig = $scopeConfig;
		$this->blackFridayConfig = $blackFridayConfig;
		$this->dataHelper = $dataHelper;
		$this->customerSession = $customerSession;
		$this->customerRepository = $customerRepository;
		$this->cookieHelper = $cookieHelper;
		$this->buyingforHelper = $buyingforHelper;
		$this->storeRepository = $storeRepository;
		$this->tiendasFactory = $tiendasFactory;
	}

	/**
	 * Setea en la cookie y en el usuario el código postal real
	 * @param $postcode
	 */
	public function setRealPostcode($postcode) {
		if ($this->customerSession->isLoggedIn()) $this->setCustomerRealPostcode($postcode);
		$this->cookieHelper->setCookie(self::BLACK_FRIDAY_POSTCODE_COOKIE, $postcode);
	}

	protected function setCustomerRealPostcode($postcode) {
		$customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
		$customer->setCustomAttribute('sopladera_real_postcode', $postcode);
		$this->customerRepository->save($customer);
	}

	/**
	 * Recupera el código postal real de la cookie o del usuario
	 */
	public function getRealPostcode() {
		return $this->getCustomerPostcode(self::BLACK_FRIDAY_POSTCODE_COOKIE, 'sopladera_real_postcode');
	}

	protected function getCustomerPostcode($cookieName, $customerAttribute) {
		$postcode = $this->cookieHelper->getCookie($cookieName);
		if (!is_null($postcode)) return $postcode;

		if ($this->customerSession->isLoggedIn()) {
			$postcode = $this->customerSession->getCustomer()->getData($customerAttribute);
			if ($postcode) return $postcode;
		}

		if ($postcode = $this->blackFridayConfig->getDefaultExitPostcode()) return $postcode;
		return '35001';
	}

	/**
	 * Borra la cookie y el dato del usuario del código postal real
	 */
	public function unsRealPostcode() {
		$this->cookieHelper->delete(self::BLACK_FRIDAY_POSTCODE_COOKIE);
		if ($this->customerSession->isLoggedIn()) $this->setCustomerRealPostcode(null);
	}

	/**
	 * Recupera el código postal de acceso a Black Friday, ya sea de la cookie o del usuario
	 */
	public function getAccessPostcode() {
		return $this->getCustomerPostcode(Cookies::POSTCODE_COOKIE_NAME, 'buying_for_postcode');
	}

	public function changeStore($postcode, $storeId = false) {
		$this->buyingforHelper->setBuyingForPostcode($postcode);
		$this->buyingforHelper->setPickupFor($storeId ?: null);
	}

	/*public function checkRealPostcodeCookie($store)
	{
		if (($this->cookieHelper->getCpCookie() == $this->getBlackFridayPostcode()) && !$this->storeIsBlackFriday($store->getCode())) {
			$this->cookieHelper->delete(Cookies::POSTCODE_COOKIE_NAME);
		}
	}*/

	/**
	 * Determina si estamos en una tienda habilitada
	 *
	 * @param bool $storeCode
	 * @return bool
	 */
	public function isBlackFridayStore($storeCode = false) {
		if ($storeCode) {
			try {
				$store = $this->storeRepository->get($storeCode);
				return $this->scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'store', $store) ? true : false;
			} catch (\Exception $e) {
				return false;
			}
		}
		return (bool)$this->scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
	}

	public function canAccessBlackFriday() {
		return !$this->isBlackFridayStore() && $this->blackFridayConfig->getCanAccessBlackFriday() && $this->dataHelper->promotionAvailable();
	}

    /**
     * @return int
     */
	public function isLoggedIn()
    {
        return (int) $this->customerSession->isLoggedIn();
    }

    /**
     * @param mixed $websiteId
     * @return bool|\Singular\Tiendas\Model\Tiendas
     */
    public function getRelatedPickupShop($websiteId)
    {
        $tiendas = $this->tiendasFactory->create()->getCollection();
        $tiendas->addFieldToFilter('website_id', $websiteId);
        $tiendas->addFieldToFilter('is_enabled', 1);
        $tiendas->addFieldToFilter('is_black_friday', 1);
        return $tiendas->getFirstItem()->getId() ? $tiendas->getFirstItem() : false;
    }
}
