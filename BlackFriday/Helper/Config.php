<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class Config
{
    const STORES_JSON = 'hiperdino_blackfriday/hidden/stores_json';
    const MAX_QUOTE_ITEMS = 'hiperdino_blackfriday/website/max_quote_items';
    const DEFAULT_EXIT_POSTCODE = 'hiperdino_blackfriday/website/exit_postcode';
    const CAN_ACCESS_BLACK_FRIDAY = 'hiperdino_blackfriday/website/access';
    const BLACK_FRIDAY_PROMOTION_IDS = 'hiperdino_blackfriday/promotions/promotion_id';

    const ENTRY_MODAL_MESSAGE = 'hiperdino_blackfriday/website_messages/entry';
    const APP_CART_MESSAGE = 'hiperdino_blackfriday/website_messages/app_cart';
    const APP_EXIT_MESSAGE = 'hiperdino_blackfriday/website_messages/app_exit';

    const IS_BLACK_FRIDAY_STORE = 'hiperdino_blackfriday/website/is_bf';
    const OVERRIDE_HEADER_DISCOUNT = 'hiperdino_blackfriday/promotions/override_header_discount';
    const OVERRIDE_CUSTOMER_DISCOUNT = 'hiperdino_blackfriday/promotions/override_customer_discount';
    const OVERRIDE_EMPLOYEE_DISCOUNT = 'hiperdino_blackfriday/promotions/override_employee_discount';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
    }

    public function getMaxQuoteItems(): int
    {
        return (int)$this->scopeConfig->getValue(self::MAX_QUOTE_ITEMS, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }

    public function getEntryModalMessage(): string
    {
        return $this->scopeConfig->getValue(self::ENTRY_MODAL_MESSAGE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ?: '';
    }

    public function getAppCartMessage(): string
    {
        return $this->scopeConfig->getValue(self::APP_CART_MESSAGE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ?: '';
    }

    public function getAppExitMessage(): string
    {
        return $this->scopeConfig->getValue(self::APP_EXIT_MESSAGE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ?: '';
    }

    public function getDefaultExitPostcode(): string
    {
        return $this->scopeConfig->getValue(self::DEFAULT_EXIT_POSTCODE, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ?: '';
    }

    public function getCanAccessBlackFriday(): bool
    {
        return $this->scopeConfig->getValue(self::CAN_ACCESS_BLACK_FRIDAY, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ? true : false;
    }

    /* BLACK FRIDAY */
    public function getStoresJson()
    {
        return $this->scopeConfig->getValue(self::STORES_JSON, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()) ?: '';
    }

    public function isBlackFridayStore($storeCode = false)
    {
        if ($storeCode) {
            try {
                $store = $this->storeRepository->get($storeCode);

                return $this->scopeConfig->getValue(self::IS_BLACK_FRIDAY_STORE, ScopeInterface::SCOPE_STORE, $store) ? true : false;
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->scopeConfig->getValue(self::IS_BLACK_FRIDAY_STORE, ScopeInterface::SCOPE_WEBSITES) ? true : false;
    }

    public function isBlackFridayWebsite($website = false)
    {
        $website = $website ?: $this->storeManager->getWebsite();

        return $this->scopeConfig->getValue(self::IS_BLACK_FRIDAY_STORE, ScopeInterface::SCOPE_WEBSITES, $website);
    }

    public function getOverrideHeaderDiscount()
    {
        return $this->scopeConfig->getValue(self::OVERRIDE_HEADER_DISCOUNT, ScopeInterface::SCOPE_WEBSITES) ? true : false;
    }

    public function getOverrideCustomerDiscount()
    {
        return $this->scopeConfig->getValue(self::OVERRIDE_CUSTOMER_DISCOUNT, ScopeInterface::SCOPE_WEBSITES) ?: '';
    }

    public function getOverrideEmployeeDiscount()
    {
        return $this->scopeConfig->getValue(self::OVERRIDE_EMPLOYEE_DISCOUNT, ScopeInterface::SCOPE_WEBSITES) ?: '';
    }

    public function getBlackFridayPromotionIds()
    {
        return explode(',', $this->getValue(self::BLACK_FRIDAY_PROMOTION_IDS) ?: "");
    }

    public function getValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }
}
