<?php

namespace Hiperdino\Dinitos\Helper;

use DateInterval;
use DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const IS_DINITOS_VISUALISATION_ENABLED_WEB = 'hiperdino_dinitos/enabled_dinitos_accumulation/enabled_web';
    const IS_DINITOS_VISUALISATION_ENABLED_APP = 'hiperdino_dinitos/enabled_dinitos_accumulation/enabled_app';

    const DINITOS_DAYS_EXPIRATION = 'hiperdino_dinitos/days_expiration/days_qty';
    const DINITOS_DAYS_EXPIRATION_DEFAULT = 365;

    const DINITOS_TEXT = 'hiperdino_dinitos/text_dinitos/dinitos_text';
    const DINITOS_CMS = 'hiperdino_dinitos/text_dinitos/dinitos_cms';
    const DINITOS_METHOD_QTY = 'hiperdino_dinitos/method/dinitos_qty';
    const DINITOS_METHOD_ACTIVE = 'hiperdino_dinitos/method/active';
    const METHOD_REPLACE = "method";

    const REWARDS_SELECTOR_MAIN_TITLE = 'hiperdino_dinitos/text_rewards_selector/main_title';
    const REWARDS_SELECTOR_TEXT_TO_EXPIRE = 'hiperdino_dinitos/text_rewards_selector/text_to_expire';
    const REWARDS_SELECTOR_TEXT_URL = 'hiperdino_dinitos/text_rewards_selector/text_url';
    const REWARDS_SELECTOR_MISSING_DIGITS_TEXT = 'hiperdino_dinitos/text_rewards_selector/reward_texts_missing_digits';
    const REWARDS_SELECTOR_REWARD_VALUE_TEXT = 'hiperdino_dinitos/text_rewards_selector/reward_value_text';

    const FILTER_CUSTOM = "hiperdino_dinitos/history_filters/filter_custom_sort";
    const EMPTY_HISTORY_TEXT = 'hiperdino_dinitos/text_history/empty_history_text';
    const EMPTY_HISTORY_TEXT_DEFAULT = 'Aún no has obtenido dinitos.';
    const MAIN_TITLE = 'hiperdino_dinitos/text_history/main_title';
    const TITLE = 'hiperdino_dinitos/text_history/title';
    const TEXT_TO_EXPIRE = 'hiperdino_dinitos/text_history/text_to_expire';
    const TEXT_URL_ONBOARDING = 'hiperdino_dinitos/text_history/text_url';
    const CMS_BLOCK_ID_ONBOARDING = 'hiperdino_dinitos/text_history/cms_block_id';
    const TEXT_LINK_SIDEBAR = 'hiperdino_dinitos/text_history/text_link_sidebar';
    const ICON_SIDEBAR = 'hiperdino_dinitos/text_history/icon_sidebar';
    const TEXT_CONCEPT_ACCUMULATION = 'hiperdino_dinitos/text_history/text_concept_accumulation';
    const TEXT_CONCEPT_REDEMPTION = 'hiperdino_dinitos/text_history/text_concept_redemption';
    const TEXT_CONCEPT_EXPIRATION = 'hiperdino_dinitos/text_history/text_concept_expiration';

    const REWARDS_ERROR_SAVING_TEXT = 'hiperdino_dinitos/error_messages/reward_checkout_error';
    const DEFAULT_REWARD_ERROR_MESSAGE = "Ha habido un error con las recompensas seleccionadas.";
    const REWARD_DISCOUNT_CODE_TEXT = "hiperdino_dinitos/error_messages/reward_coupon_error";
    const DEFAULT_REWARD_DISCOUNT_CODE_MESSAGE = "El código de cupón no es válido.";

    const REWARDS_PRODUCTS_PERCENTAGE = 'hiperdino_dinitos/reward_products_discount/discount_percentage';
    const REWARDS_SHIPPING_PERCENTAGE = 'hiperdino_dinitos/reward_shipping_discount/discount_percentage';
    const REWARD_DEFAULT_PERCENTAGE = 0;
    const REWARDS_PRODUCTS_APPLY_OTHER_PRODUCTS = 'hiperdino_dinitos/reward_products_discount/discount_other_products';
    const REWARD_BAGS_TOTAL_DISCOUNT_LABEL = 'hiperdino_dinitos/reward_products_discount/bags_discount_label';
    const REWARD_BAGS_TOTAL_DISCOUNT_DEFAULT_LABEL = 'Tus bolsas';

    const DINITOS_EXPIRATION_REMINDER = "hiperdino_dinitos/remind_config/expiration_send_interval";
    const DINITOS_EXPIRATION_REMINDER_DEFAULT = [14];
    const DINITOS_REMINDER_TEXT = "hiperdino_dinitos/remind_config/push_text";
    const DINITOS_REMINDER_TEXT_DEFAULT = '';
    const DINITOS_REMINDER_ALERT_TITLE = "hiperdino_dinitos/remind_config/alert_title";
    const DINITOS_REMINDER_ALERT_TITLE_DEFAULT = "¡Tienes dinitos próximos a caducar!";
    const DINITOS_HISTORY_EXPIRATION_DAYS = 'hiperdino_dinitos/remind_config/days_qty';

    const DEFAULT_MAIN_TITLE = 'Mis Dinitos';
    const DEFAULT_TITLE = 'Historial de Dinitos';
    const DEFAULT_TEXT_TO_EXPIRE = 'próximos a caducar, válidos hasta';
    const DEFAULT_TEXT_URL_ONBOARDING = '¿Cómo usar tus Dinitos?';
    const DEFAULT_CMS_BLOCK_ID_ONBOARDING = '';
    const DEFAULT_TEXT_LINK_SIDEBAR = 'Mis Dinitos';
    const DEFAULT_ICON_SIDEBAR = '';
    const DEFAULT_EMPTY_HISTORY_TEXT = 'En este momento no tiene dinitos acumulados';

    const DEFAULT_MAIN_TITLE_REWARDS = '¿Cómo quieres usar tus Dinitos?';
    const DEFAULT_TEXT_TO_EXPIRE_REWARDS = 'próximos a caducar, válidos hasta';
    const DEFAULT_TEXT_URL_REWARDS = 'Te explicamos cómo usar tus Dinitos aquí.';
    const DEFAULT_MISSING_DIGITS_TEXT_REWARDS = 'Dinitos son necesarios';
    const DEFAULT_REWARD_VALUE_TEXT_REWARDS = 'Dinitos por cada compra';

    const TEXT_CONCEPT_ACCUMULATION_DEFAULT = 'Movimiento de acumulación';
    const TEXT_CONCEPT_REDEMPTION_DEFAULT = 'Movimiento de canjeo';
    const TEXT_CONCEPT_EXPIRATION_DEFAULT = 'Movimiento de caducidad';

    const POSITION_NATIVE_DEFAULT = 'position';
    const POSITION_CUSTOM_DEFAULT = 'Defecto';
    const OBTAINED_NATIVE_DEFAULT = 'obtained';
    const OBTAINED_CUSTOM_DEFAULT = 'Obtenidos';
    const REDEEMED_NATIVE_DEFAULT = 'redeemed';
    const REDEEMED_CUSTOM_DEFAULT = 'Canjeados';
    const EXPIRED_NATIVE_DEFAULT = 'expired';
    const EXPIRED_CUSTOM_DEFAULT = 'Caducados';
    const REFUND_NATIVE_DEFAULT = 'refunded';
    const REFUND_CUSTOM_DEFAULT = 'Reembolsados';
    const DEDUCTION_NATIVE_DEFAULT = 'deducted';
    const DEDUCTION_CUSTOM_DEFAULT = 'Deducidos';

    const TEXT_USE_IN = 'hiperdino_dinitos/text_cart_sidebar/dinitos_use';
    const TEXT_USE_IN_DEFAULT = 'Puedes usarlos en: ';
    const TEXT_DINITOS_LEFT = 'hiperdino_dinitos/text_cart_sidebar/dinitos_left';
    const TEXT_DINITOS_LEFT_DEFAULT = 'Te faltan ';
    const TEXT_CAN_ACHIEVE = 'hiperdino_dinitos/text_cart_sidebar/dinitos_to_achieve';
    const TEXT_CAN_ACHIEVE_DEFAULT = 'Puedes conseguir: ';
    const TEXT_DINITOS_OBTAINED = 'hiperdino_dinitos/text_cart_sidebar/dinitos_obtained';
    const TEXT_DINITOS_OBTAINED_DEFAULT = 'Tienes ';
    const EXCLUDE_SELECTED_REWARDS = 'hiperdino_dinitos/text_cart_sidebar/exclude_selected_rewards';
    const EXCLUDE_SELECTED_REWARDS_DEFAULT = false;


    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getDinitosDaysExpiration()
    {
        return $this->getValue(self::DINITOS_DAYS_EXPIRATION) ?? self::DINITOS_DAYS_EXPIRATION_DEFAULT;
    }

    public function getDinitosMessage(): string
    {
        $dinitosText = $this->getValue(self::DINITOS_TEXT);
        $dinitosText = str_replace(['#@', '@#', '<br>'], ['<span class="bp_subtitle">', '</span>', ''], $dinitosText ?: "");

        return sprintf($dinitosText, $this->getDinitosQty('delivery'), $this->getDinitosQty('pickup'), $this->getDinitosQty('pickuppoint'));
    }

    public function getDinitosConfigMessage()
    {
        return $this->getValue(self::DINITOS_TEXT) ?? '';
    }

    public function getDinitosCms()
    {
        return $this->getValue(self::DINITOS_CMS) ?? '';
    }

    public function getDinitosQty(string $deliveryMethod)
    {
        return $this->getValue(str_replace(self::METHOD_REPLACE, $deliveryMethod, self::DINITOS_METHOD_QTY));
    }

    public function getDinitosActive(string $deliveryMethod)
    {
        return $this->getValue(str_replace(self::METHOD_REPLACE, $deliveryMethod, self::DINITOS_METHOD_ACTIVE));
    }

    /**
     * Get all reward-related texts.
     */
    public function getRewardTexts(): array
    {
        return [
            'main_title' => $this->getValue(self::REWARDS_SELECTOR_MAIN_TITLE) ?? self::DEFAULT_MAIN_TITLE_REWARDS,
            'text_to_expire' => $this->getValue(self::REWARDS_SELECTOR_TEXT_TO_EXPIRE) ?? self::DEFAULT_TEXT_TO_EXPIRE_REWARDS,
            'text_url' => $this->getValue(self::REWARDS_SELECTOR_TEXT_URL) ?? self::DEFAULT_TEXT_URL_REWARDS,
            'cms_block_id' => $this->getValue(self::CMS_BLOCK_ID_ONBOARDING) ?? self::DEFAULT_CMS_BLOCK_ID_ONBOARDING,
            'missing_digits_text' => $this->getValue(self::REWARDS_SELECTOR_MISSING_DIGITS_TEXT) ?? self::DEFAULT_MISSING_DIGITS_TEXT_REWARDS,
            'reward_value_text' => $this->getRewardValueText(),
            'dinitos_left_text' => $this->getDinitosLeftText(),
            'dinitos_use_text' => $this->getDinitosToUseInText(),
            'dinitos_achieve_text' => $this->getDinitosToAchieveText(),
            'dinitos_obtained' => $this->getDinitosObtainedText(),
            'exclude_selected_rewards' => $this->excludeSelectedRewards(),
            'bags_reward_text' => $this->getRewardsTotalDiscountLabel()
        ];
    }

    /**
     * Get all history data including titles, texts, and URLs.
     *
     * @throws NoSuchEntityException
     */
    public function getTextHistory(): array
    {
        return [
            'main_title' => $this->getValue(self::MAIN_TITLE) ?? self::DEFAULT_MAIN_TITLE,
            'title' => $this->getValue(self::TITLE) ?? self::DEFAULT_TITLE,
            'text_to_expire' => $this->getValue(self::TEXT_TO_EXPIRE) ?? self::DEFAULT_TEXT_TO_EXPIRE,
            'text_url_onboarding' => $this->getValue(self::TEXT_URL_ONBOARDING) ?? self::DEFAULT_TEXT_URL_ONBOARDING,
            'cms_block_id_onboarding' => $this->getValue(self::CMS_BLOCK_ID_ONBOARDING) ?? self::DEFAULT_CMS_BLOCK_ID_ONBOARDING,
            'text_link_sidebar' => $this->getValue(self::TEXT_LINK_SIDEBAR) ?? self::DEFAULT_TEXT_LINK_SIDEBAR,
            'icon_sidebar' => $this->getIconSidebarHistory() ?? self::DEFAULT_ICON_SIDEBAR,
            'empty_history_text' => $this->getEmptyHistoryText() ?? self::DEFAULT_EMPTY_HISTORY_TEXT,
        ];
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getIconSidebarHistory()
    {
        $iconSidebar = $this->getValue(self::ICON_SIDEBAR);
        if ($iconSidebar) {
            $iconSidebar = $this->getMediaUrl() . "dinitos/history/icon_sidebar" . $iconSidebar;
        }

        return $iconSidebar;
    }

    public function getTextConceptAccumulation(): string
    {
        return $this->getValue(self::TEXT_CONCEPT_ACCUMULATION) ?: self::TEXT_CONCEPT_ACCUMULATION_DEFAULT;
    }

    public function getTextConceptRedemption(): string
    {
        return $this->getValue(self::TEXT_CONCEPT_REDEMPTION) ?: self::TEXT_CONCEPT_REDEMPTION_DEFAULT;
    }

    public function getTextConceptExpiration(): string
    {
        return $this->getValue(self::TEXT_CONCEPT_EXPIRATION) ?: self::TEXT_CONCEPT_EXPIRATION_DEFAULT;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function getFilterHistory()
    {
        $filtersSerialize = $this->getValue(self::FILTER_CUSTOM);

        $filters = $this->parseFilterCustom($filtersSerialize);

        if (empty($filters)) {
            return $this->getDefaultFilter();
        }

        return $filters;
    }

    private function getDefaultFilter()
    {
        return [
            [
                'title_native' => self::POSITION_NATIVE_DEFAULT,
                'title_custom' => self::POSITION_CUSTOM_DEFAULT
            ],
            [
                'title_native' => self::OBTAINED_NATIVE_DEFAULT,
                'title_custom' => self::OBTAINED_CUSTOM_DEFAULT
            ],
            [
                'title_native' => self::REDEEMED_NATIVE_DEFAULT,
                'title_custom' => self::REDEEMED_CUSTOM_DEFAULT
            ],
            [
                'title_native' => self::REFUND_NATIVE_DEFAULT,
                'title_custom' => self::REFUND_CUSTOM_DEFAULT
            ],
            [
                'title_native' => self::DEDUCTION_NATIVE_DEFAULT,
                'title_custom' => self::DEDUCTION_CUSTOM_DEFAULT
            ],
            [
                'title_native' => self::EXPIRED_NATIVE_DEFAULT,
                'title_custom' => self::EXPIRED_CUSTOM_DEFAULT
            ]
        ];
    }

    private function parseFilterCustom($filtersSerialize)
    {
        $decode = json_decode($filtersSerialize, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($decode)) {
            return array();
        }

        $resultArray = array();

        foreach ($decode as $key => $item) {
            $resultArray[] = array(
                'title_native' => $item['title_native'],
                'title_custom' => $item['title_custom']
            );
        }

        return $resultArray;
    }

    public function isDinitosAccumulatedEnabledWeb($store = null): bool
    {
        return $this->getValue(self::IS_DINITOS_VISUALISATION_ENABLED_WEB, $store) ?? false;
    }

    public function isDinitosAccumulatedEnabledApp(): bool
    {
        return $this->getValue(self::IS_DINITOS_VISUALISATION_ENABLED_APP) ?? false;
    }

    public function getRewardsErrorMessage()
    {
        return $this->getValue(self::REWARDS_ERROR_SAVING_TEXT) ?: self::DEFAULT_REWARD_ERROR_MESSAGE;
    }

    public function getDinitosReminderConfig(): ?array
    {
        $dinitosConfig = $this->getValue(self::DINITOS_EXPIRATION_REMINDER);

        return $dinitosConfig ? $this->parseDinitosReminderConfig($dinitosConfig) : self::DINITOS_EXPIRATION_REMINDER_DEFAULT;
    }

    public function parseDinitosReminderConfig($reminderConfig): ?array
    {
        $result = [];
        foreach (json_decode($reminderConfig, associative: true) as $reminderConfigChild) {
            $result[] = $reminderConfigChild['send_interval'];
        }
        $orderedResult = rsort($result);

        return $result;
    }

    public function getReminderPushText()
    {
        return $this->getValue(self::DINITOS_REMINDER_TEXT) ?: self::DINITOS_REMINDER_TEXT_DEFAULT;
    }

    public function getReminderAlertTitle()
    {
        return $this->getValue(self::DINITOS_REMINDER_ALERT_TITLE) ?: self::DINITOS_REMINDER_ALERT_TITLE_DEFAULT;
    }

    public function getEmptyHistoryText()
    {
        return $this->getValue(self::EMPTY_HISTORY_TEXT) ?: self::EMPTY_HISTORY_TEXT_DEFAULT;
    }

    public function getDaysToExpirationFilterConfig()
    {
        return $this->getValue(self::DINITOS_HISTORY_EXPIRATION_DAYS);
    }

    public function getDateFromExpirationFilterConfig()
    {
        $days = $this->getDaysToExpirationFilterConfig();
        if ($days) {
            $date = new DateTime('00:00');
            $date->add(new DateInterval("P{$days}D"));

            return $date;
        }

        return null;
    }

    public function getRewardsProductsPercentage()
    {
        return $this->getValue(self::REWARDS_PRODUCTS_PERCENTAGE) ?? self::REWARD_DEFAULT_PERCENTAGE;
    }


    public function getRewardsShippingPercentage()
    {
        return $this->getValue(self::REWARDS_SHIPPING_PERCENTAGE) ?? self::REWARD_DEFAULT_PERCENTAGE;
    }

    public function getBagSkus()
    {
        return ($bagsSkus = $this->scopeConfig->getValue("singular_delivery/preparation/bag_skus")) ? explode(",", $bagsSkus) : [];
    }

    public function getApplyRewardOthersProducts()
    {
        return $this->getValue(self::REWARDS_PRODUCTS_APPLY_OTHER_PRODUCTS);
    }

    public function getRewardsTotalDiscountLabel()
    {
        return $this->getValue(self::REWARD_BAGS_TOTAL_DISCOUNT_LABEL) ?? self::REWARD_BAGS_TOTAL_DISCOUNT_DEFAULT_LABEL;
    }

    public function getDinitosLeftText()
    {
        return $this->getValue(self::TEXT_DINITOS_LEFT) ?: self::TEXT_DINITOS_LEFT_DEFAULT;
    }

    public function getDinitosToUseInText()
    {
        return $this->getValue(self::TEXT_USE_IN) ?: self::TEXT_USE_IN_DEFAULT;
    }

    public function getDinitosToAchieveText()
    {
        return $this->getValue(self::TEXT_CAN_ACHIEVE) ?: self::TEXT_CAN_ACHIEVE_DEFAULT;
    }

    public function getRewardValueText()
    {
        return $this->getValue(self::REWARDS_SELECTOR_REWARD_VALUE_TEXT) ?: self::DEFAULT_REWARD_VALUE_TEXT_REWARDS;
    }

    public function getDinitosObtainedText()
    {
        return $this->getValue(self::TEXT_DINITOS_OBTAINED) ?: self::TEXT_DINITOS_OBTAINED_DEFAULT;
    }

    public function excludeSelectedRewards()
    {
        return $this->getValue(self::EXCLUDE_SELECTED_REWARDS) ?: self::EXCLUDE_SELECTED_REWARDS_DEFAULT;
    }

    public function getDiscountCodeErrorMessage()
    {
        return $this->getValue(self::REWARD_DISCOUNT_CODE_TEXT) ?: self::DEFAULT_REWARD_DISCOUNT_CODE_MESSAGE;
    }

    private function getValue($path, $store = null)
    {
        if (!$store) {
            $store = $this->storeManager->getStore();
        }

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
    }
}