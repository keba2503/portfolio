<?php

namespace Hiperdino\Anniversary2020\Helper;

use DateTime;
use DateTimeZone;
use Exception;
use Hiperdino\Anniversary2020\Model\Config\Source\WeekListMode;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const ANNIVERSARY_ENABLED = "hiperdino_anniversary2020/general/enabled";
    const ID_RAFFLE = "hiperdino_anniversary2020/general/id_raffle";
    const ANNIVERSARY_PROMO_START = "hiperdino_anniversary2020/general/promo_start";
    const ANNIVERSARY_PROMO_END = "hiperdino_anniversary2020/general/promo_end";
    const FINAL_PROMOTION_TITLE = "hiperdino_anniversary2020/general/final_promotion_title";
    const PROMO_MESSAGE_OUT_PROMOTION = "hiperdino_anniversary2020/general/message_out_promotion";
    const PROMO_TEXT_TERMS_PROMOTION = "hiperdino_anniversary2020/general/text_terms_promotion";
    const PROMO_TERMS_ACCEPT = "hiperdino_anniversary2020/general/terms_accept";
    const PROMO_TERMS_ACCEPT_APP = "hiperdino_anniversary2020/general/terms_accept_app";
    const PROMO_URL_PAGE_TERMS_PROMOTION1 = "hiperdino_anniversary2020/general/url_page_terms_promotion1";
    const PROMO_URL_PAGE_TERMS_PROMOTION2 = "hiperdino_anniversary2020/general/url_page_terms_promotion2";
    const PROMO_TEXT_TERMS_PROMOTION_APP = "hiperdino_anniversary2020/general/text_terms_promotion_app";
    const PROMO_URL_PAGE_TERMS_PROMOTION1_APP = "hiperdino_anniversary2020/general/url_page_terms_promotion1_app";
    const PROMO_URL_PAGE_TERMS_PROMOTION2_APP = "hiperdino_anniversary2020/general/url_page_terms_promotion2_app";
    const PROMO_TEXT_EMAIL_PROMOTION = "hiperdino_anniversary2020/general/text_email_promotion";
    const TEXT_RGPD = "hiperdino_anniversary2020/general/text_rgpd";
    const URL_EMAIL_PROMOTION = "hiperdino_anniversary2020/general/url_email_promotion";

    const RASCA_TITLE = "hiperdino_anniversary2020/register_rasca/tittle";
    const RASCA_TEXT_OUT_PROMO = "hiperdino_anniversary2020/register_rasca/text_out_promotion";
    const RASCA_TEXT_PROMO_HEADER = "hiperdino_anniversary2020/register_rasca/text_promotion_header";
    const RASCA_TEXT_PROMO_FOOTER = "hiperdino_anniversary2020/register_rasca/text_promotion_footer";
    const RASCA_IMAGE_PROMO = "hiperdino_anniversary2020/register_rasca/image_promotion";
    const RASCA_IMAGE_CODE = "hiperdino_anniversary2020/register_rasca/image_code_participation";
    const RASCA_ERROR_MAX_WRONG_RASCAS_REGISTERED = "hiperdino_anniversary2020/register_rasca/error_max_wrong_rascas_registered";
    const RASCA_ERROR_INVALID_RASCA = "hiperdino_anniversary2020/register_rasca/error_invalid_rasca";
    const RASCA_ERROR_REGISTERED_RASCA = "hiperdino_anniversary2020/register_rasca/error_registered_rasca";
    const RASCA_ERROR_REGISTERED_MY_RASCA = "hiperdino_anniversary2020/register_rasca/error_registered_my_rasca";
    const RASCA_NUM_MAX_WRONG_RASCAS_REGISTERED = "hiperdino_anniversary2020/register_rasca/num_max_wrong_rascas_registered";

    const NUM_CHARACTERS_RASCA = 7;

    const ACTIVE_WEEK = "hiperdino_anniversary2020/week_config/active_week";
    const WEEK_DEFAULT = "week_1";
    const WEEK = "week_";
    const START_WEEKLY_PROMOTION = "start_weekly_promotion";
    const WEEK_CONFIG_GROUP = "week_config";
    const SECTION = "hiperdino_anniversary2020";
    const TITLE_WEEKLY_PROMOTION = "tittle_weekly_promotion";
    const TEXT_WEEKLY_PROMOTION_HEADER = "text_weekly_promotion_header";
    const TEXT_WEEKLY_PROMOTION_FOOTER = "text_weekly_promotion_footer";
    const IMAGE_WEEKLY_PROMOTION = "image_weekly_promotion";

    const RASCA_MENU_CUSTOMER = "hiperdino_anniversary2020/menu_config/title_menu_customer";
    const MENU_TITLE = "hiperdino_anniversary2020/menu_config/title_menu";
    const MENU_URL_CMS_ANNIVERSARY = "hiperdino_anniversary2020/menu_config/url_anniversary";
    const MENU_URL_CMS_RASCA = "hiperdino_anniversary2020/menu_config/url_cms_rasca";
    const MENU_URL_CMS_RAFFLES = "hiperdino_anniversary2020/menu_config/url_cms_raffles";
    const URL_CMS_DIRECT_AWARDS = "hiperdino_anniversary2020/menu_config/url_cms_direct_awards";
    const URL_CMS_PARTICIPATE = "hiperdino_anniversary2020/menu_config/url_cms_participate";
    const URL_CMS_LEGAL = "hiperdino_anniversary2020/menu_config/url_cms_legal";

    const QUEUE_HISTORY_ENABLED = "hiperdino_anniversary2020/queue/enabled_history";
    const QUEUE_SUCCES_DAYS = "hiperdino_anniversary2020/queue/delete_success_days";
    const QUEUE_ERRORS_DAYS = "hiperdino_anniversary2020/queue/delete_errors_days";
    const QUEUE_TRIES = "hiperdino_anniversary2020/queue/max_times";
    const QUEUE_DEFAULT_DAYS = 30;
    const QUEUE_DEFAULT_DAYS_ERRORS = 60;

    const BASE_URL = "hiperdino_anniversary2020/raffle/api_base_url";
    const UID = "hiperdino_anniversary2020/raffle/login_uidactividad";
    const USERNAME = "hiperdino_anniversary2020/raffle/login_username";
    const PASSWORD = "hiperdino_anniversary2020/raffle/login_password";

    // Textos tarjetas Mis Rascas
    const TEXT_CARD_NOT_RAFFLE = 'hiperdino_anniversary2020/text_participations_cards/text_card_not_raffle';
    const TEXT_CARD_RAFFLE = 'hiperdino_anniversary2020/text_participations_cards/text_card_raffle';
    const TEXT_BOTON = 'hiperdino_anniversary2020/text_participations_cards/text_boton';
    const TEXT_BOTON_SCRATCH = 'hiperdino_anniversary2020/text_participations_cards/text_boton_scratch';

    // Texto e imagen de carrousel de Rascas
    const SCRATCH_START = 'hiperdino_anniversary2020/participations_carrousel/scratch_start';
    const SCRATCH_END = 'hiperdino_anniversary2020/participations_carrousel/scratch_end';
    const TEXT_TITLE_CARROUSEL = 'hiperdino_anniversary2020/participations_carrousel/text_title_carrousel';
    const IMAGE_SECTION_CARROUSEL = 'hiperdino_anniversary2020/participations_carrousel/image_carrousel';
    const IMAGE_SCRATCH = 'hiperdino_anniversary2020/participations_carrousel/image_scratch';
    const TEXT_MODAL_SCRATCH = 'hiperdino_anniversary2020/participations_carrousel/text_modal_scratch';
    const TITLE_MODAL_SCRATCH = 'hiperdino_anniversary2020/participations_carrousel/title_modal_scratch';
    const SCRATCHED_PERCENTAGE = 'hiperdino_anniversary2020/participations_carrousel/scratched_percentage';

    // Configuración de modales
    const TEXT_MODAL_PRIZE = 'hiperdino_anniversary2020/participations_modals/text_modal_prize';
    const BACKGROUND_MODALS = 'hiperdino_anniversary2020/participations_modals/background_modal';
    const BACKGROUND_MODALS_APP = 'hiperdino_anniversary2020/participations_modals/background_modal_app';
    const IMAGE_PARTICIPATION_WITHOUT_PRIZE = 'hiperdino_anniversary2020/participations_modals/image_participation_without_prize';
    const IMAGE_PARTICIPATION_DEFAULT_PRIZE = 'hiperdino_anniversary2020/participations_modals/image_default_prize';

    // Textos de registro de participaciones
    const TITLE_REGISTER_PARTICIPATION = 'hiperdino_anniversary2020/register_participation/title_register_participation';
    const TEXT_CONFIRM_REGISTER = 'hiperdino_anniversary2020/register_participation/text_confirm_register';

    // Push
    const PUSH_PARTICIPATION_MESSAGE = 'hiperdino_anniversary2020/push/participation_message';
    const PUSH_PARTICIPATION_INTERNAL_PATH = 'hiperdino_anniversary2020/push/participation_internal_path';

    //Email
    const EMAIL_ENABLED = 'hiperdino_anniversary2020/participation_winner/enabled';
    const EMAIL_IDENTITY = 'hiperdino_anniversary2020/participation_winner/identity';
    const EMAIL_TEMPLATE = 'hiperdino_anniversary2020/participation_winner/template';

    // Homai
    const HOMAI_PROMOTION_IDS = 'hiperdino_anniversary2020/homai/promotion_ids';
    const HOMAI_PRIZE_TEXT = 'hiperdino_anniversary2020/homai/prize_text';

    protected StoreManagerInterface $storeManager;
    protected ScopeConfigInterface $scopeConfig;
    protected Logger $logger;
    protected WeekListMode $weekListMode;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param WeekListMode $weekListMode
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        WeekListMode $weekListMode
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->weekListMode = $weekListMode;
    }

    public function isAnniversaryEnabled()
    {
        return $this->getValue(self::ANNIVERSARY_ENABLED);
    }

    public function getIdRaffle()
    {
        return $this->getValue(self::ID_RAFFLE);
    }

    public function getPromoStart()
    {
        return $this->getValue(self::ANNIVERSARY_PROMO_START);
    }

    public function getPromoEnd()
    {
        return $this->getValue(self::ANNIVERSARY_PROMO_END);
    }

    /**
     * @return bool
     */
    public function isPromotionAvailable(): bool
    {
        if (!$this->isAnniversaryEnabled()) {
            return false;
        }

        try {
            $timezone = new DateTimeZone('Atlantic/Canary');
            $today = new DateTime('now', $timezone);

            $fromDate = new DateTime($this->getPromoStart() ?: "");
            $toDate = new DateTime($this->getPromoEnd() ?: "");
            date_time_set($toDate, 23, 59, 59);

            if ($fromDate <= $today && $toDate >= $today) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getFinalPromotionTitle()
    {
        return $this->getValue(self::FINAL_PROMOTION_TITLE);
    }

    public function getMessageOutPromotion()
    {
        return $this->getValue(self::PROMO_MESSAGE_OUT_PROMOTION);
    }

    public function getTextTermsPromotion()
    {
        return $this->getValue(self::PROMO_TEXT_TERMS_PROMOTION);
    }

    public function getTermsAccept()
    {
        return $this->getValue(self::PROMO_TERMS_ACCEPT);
    }

    public function getTextTermsAcceptApp()
    {
        return $this->getValue(self::PROMO_TERMS_ACCEPT_APP);
    }

    public function getUrlPageTermsPromotion1()
    {
        return $this->getValue(self::PROMO_URL_PAGE_TERMS_PROMOTION1);
    }

    public function getUrlPageTermsPromotion2()
    {
        return $this->getValue(self::PROMO_URL_PAGE_TERMS_PROMOTION2);
    }

    public function getTextTermsPromotionApp()
    {
        return $this->getValue(self::PROMO_TEXT_TERMS_PROMOTION_APP);
    }

    public function getUrlPageTermsPromotion1App()
    {
        return $this->getValue(self::PROMO_URL_PAGE_TERMS_PROMOTION1_APP);
    }

    public function getUrlPageTermsPromotion2App()
    {
        return $this->getValue(self::PROMO_URL_PAGE_TERMS_PROMOTION2_APP);
    }

    public function getTextEmailPromotion()
    {
        return $this->getValue(self::PROMO_TEXT_EMAIL_PROMOTION);
    }

    public function getTextRgpd()
    {
        return $this->getValue(self::TEXT_RGPD);
    }

    public function getUrlEmailPromotion()
    {
        return $this->getValue(self::URL_EMAIL_PROMOTION);
    }

    public function getRegisterRascaTitle()
    {
        return $this->getValue(self::RASCA_TITLE);
    }

    public function getRascaMenuCustomer()
    {
        return $this->getValue(self::RASCA_MENU_CUSTOMER);
    }

    public function getRegisterRascaTextOutPromo()
    {
        return $this->getValue(self::RASCA_TEXT_OUT_PROMO);
    }

    public function getRegisterRascaTextPromoHeader()
    {
        return $this->getValue(self::RASCA_TEXT_PROMO_HEADER);
    }

    public function getRegisterRascaTextPromoFooter()
    {
        return $this->getValue(self::RASCA_TEXT_PROMO_FOOTER);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getRegisterRascaImagePromo()
    {
        $imagePromo = $this->getValue(self::RASCA_IMAGE_PROMO);
        if ($imagePromo) {
            $imagePromo = $this->getMediaUrl() . "anniversary2020/image_promotion/" . $imagePromo;
        }

        return $imagePromo;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getRegisterRascaImageCode()
    {
        $imageCode = $this->getValue(self::RASCA_IMAGE_CODE);
        if ($imageCode) {
            $imageCode = $this->getMediaUrl() . "anniversary2020/image_code_participation/" . $imageCode;
        }

        return $imageCode;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function getNumCharactersRasca()
    {
        return self::NUM_CHARACTERS_RASCA;
    }

    public function getErrorMaxWrongRascasRegistered()
    {
        return $this->getValue(self::RASCA_ERROR_MAX_WRONG_RASCAS_REGISTERED);
    }

    public function getErrorInvalidRasca()
    {
        return $this->getValue(self::RASCA_ERROR_INVALID_RASCA);
    }

    public function getErrorRegisteredRasca()
    {
        return $this->getValue(self::RASCA_ERROR_REGISTERED_RASCA);
    }

    public function getErrorRegisteredMyRasca()
    {
        return $this->getValue(self::RASCA_ERROR_REGISTERED_MY_RASCA);
    }

    public function getTitleMenu()
    {
        return $this->getValue(self::MENU_TITLE);
    }

    public function getUrlCmsRasca()
    {
        return $this->getValue(self::MENU_URL_CMS_RASCA);
    }

    public function getUrlCmsAnniversary()
    {
        return $this->getValue(self::MENU_URL_CMS_ANNIVERSARY);
    }

    public function getUrlCmsRaffles()
    {
        return $this->getValue(self::MENU_URL_CMS_RAFFLES);
    }

    public function getUrlCmsDirectAwards()
    {
        return $this->getValue(self::URL_CMS_DIRECT_AWARDS);
    }

    public function getUrlCmsParticipate()
    {
        return $this->getValue(self::URL_CMS_PARTICIPATE);
    }

    public function getUrlCmsLegal()
    {
        return $this->getValue(self::URL_CMS_LEGAL);
    }

    public function getTitleWeeklyPromotion($activeWeek = false)
    {
        $activeWeek = $activeWeek ?: $this->getActiveWeek();

        return $this->getScopeWeekConfigValue($activeWeek . "/" . self::TITLE_WEEKLY_PROMOTION);
    }

    public function getTitleWeekly($activeWeek = false)
    {
        $activeWeek = $activeWeek ?: '';

        return $this->getScopeWeekConfigValue($activeWeek . "/" . self::TITLE_WEEKLY_PROMOTION);
    }

    public function getTextWeeklyPromotionHeader()
    {
        $activeWeek = $this->getActiveWeek();

        return $this->getScopeWeekConfigValue($activeWeek . "/" . self::TEXT_WEEKLY_PROMOTION_HEADER);
    }

    public function getTextWeeklyPromotionFooter()
    {
        $activeWeek = $this->getActiveWeek();

        return $this->getScopeWeekConfigValue($activeWeek . "/" . self::TEXT_WEEKLY_PROMOTION_FOOTER);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getImageWeeklyPromotion()
    {
        $activeWeek = $this->getActiveWeek();
        $imagePromo = $this->getScopeWeekConfigValue($activeWeek . "/" . self::IMAGE_WEEKLY_PROMOTION);
        if ($imagePromo) {
            $imagePromo = $this->getMediaUrl() . "anniversary2020/image_weekly_promotion/" . $imagePromo;
        }

        return $imagePromo;
    }

    private function getActiveWeekForStartDate()
    {
        try {
            $timezone = new DateTimeZone('Atlantic/Canary');
            $today = new DateTime('now', $timezone);
            $numberWeek = count($this->weekListMode->toOptionArray());

            do {
                $startWeek = $this->getScopeWeekConfigValue(self::WEEK . $numberWeek . "/" . self::START_WEEKLY_PROMOTION);
                if ($startWeek) {
                    try {
                        $fromDateWeek = new DateTime($startWeek);
                        if ($fromDateWeek <= $today) {
                            return self::WEEK . $numberWeek;
                        }
                    } catch (Exception $ex) {
                        $this->logger->log($ex);
                    }
                }
                $numberWeek--;
            } while ($numberWeek > 0);

            return self::WEEK_DEFAULT;
        } catch (Exception $ex) {
            $this->logger->log($ex);
        }

        return $this->getScopeWeekConfigValue(self::ACTIVE_WEEK);
    }

    public function getActiveWeek()
    {
        return $this->getActiveWeekForStartDate();
    }

    private function getScopeWeekConfigValue($field)
    {
        return $this->scopeConfig->getValue(self::SECTION . "/" . self::WEEK_CONFIG_GROUP . "/" . $field);
    }

    public function getScopeRegisterRascaValue()
    {
        return $this->getValue(self::RASCA_NUM_MAX_WRONG_RASCAS_REGISTERED);
    }

    public function getHistoryQueueEnabled()
    {
        return $this->getValue(self::QUEUE_HISTORY_ENABLED);
    }

    public function getMaxDaysQueueRegisterSuccess()
    {
        return $this->getValue(self::QUEUE_SUCCES_DAYS) ?? self::QUEUE_DEFAULT_DAYS;
    }

    public function getMaxDaysQueueRegisterErrors()
    {
        return $this->getValue(self::QUEUE_ERRORS_DAYS) ?? self::QUEUE_DEFAULT_DAYS_ERRORS;
    }

    public function getMaxQueueTries()
    {
        return $this->getValue(self::QUEUE_TRIES);
    }

    public function getTextCardNotRaffle()
    {
        return $this->getValue(self::TEXT_CARD_NOT_RAFFLE);
    }

    public function getTextCardRaffle()
    {
        return $this->getValue(self::TEXT_CARD_RAFFLE);
    }

    public function getTextBoton()
    {
        return $this->getValue(self::TEXT_BOTON);
    }

    public function getTextBotonScratch()
    {
        return $this->getValue(self::TEXT_BOTON_SCRATCH);
    }

    public function getTextTitleCarrousel()
    {
        return $this->getValue(self::TEXT_TITLE_CARROUSEL);
    }

    public function getImageCarrousel()
    {
        $imageCarrousel = $this->getValue(self::IMAGE_SECTION_CARROUSEL);

        if ($imageCarrousel) {
            $imageCarrousel = $this->getMediaUrl() . "anniversary2020/image_carrousel/" . $imageCarrousel;
        }

        return $imageCarrousel;
    }

    public function getImageScratch()
    {
        $imageScratch = $this->getValue(self::IMAGE_SCRATCH);

        if ($imageScratch) {
            $imageScratch = $this->getMediaUrl() . "anniversary2020/image_scratch/" . $imageScratch;
        }

        return $imageScratch;
    }

    public function getTextModalPrize()
    {
        return $this->getValue(self::TEXT_MODAL_PRIZE);
    }

    public function getBackgroundModal()
    {
        $backgroundImg = $this->getValue(self::BACKGROUND_MODALS);

        if ($backgroundImg) {
            $backgroundImg = $this->getMediaUrl() . "anniversary2020/background_modal/" . $backgroundImg;
        }

        return $backgroundImg;
    }

    public function getBackgroundModalApp()
    {
        $backgroundImg = $this->getValue(self::BACKGROUND_MODALS_APP);

        if ($backgroundImg) {
            $backgroundImg = $this->getMediaUrl() . "anniversary2020/background_modal_app/" . $backgroundImg;
        }

        return $backgroundImg;
    }

    public function getImageWithoutPrize()
    {
        $imageNoPrize = $this->getValue(self::IMAGE_PARTICIPATION_WITHOUT_PRIZE);

        if ($imageNoPrize) {
            $imageNoPrize = $this->getMediaUrl() . "anniversary2020/image_participation_without_prize/" . $imageNoPrize;
        }

        return $imageNoPrize;
    }

    public function getImageDefaultPrize()
    {
        $imageNoPrize = $this->getValue(self::IMAGE_PARTICIPATION_DEFAULT_PRIZE);

        if ($imageNoPrize) {
            $imageNoPrize = $this->getMediaUrl() . "anniversary2020/image_default_prize/" . $imageNoPrize;
        }

        return $imageNoPrize;
    }

    public function getTitleRegisterParticipation()
    {
        return $this->getValue(self::TITLE_REGISTER_PARTICIPATION);
    }

    public function getTextConfirmRegister()
    {
        return $this->getValue(self::TEXT_CONFIRM_REGISTER);
    }

    public function getBaseUrl()
    {
        return $this->getValue(self::BASE_URL);
    }

    public function getUserName()
    {
        return $this->getValue(self::USERNAME);
    }

    public function getUid()
    {
        return $this->getValue(self::UID);
    }

    public function getPassword()
    {
        return $this->getValue(self::PASSWORD);
    }

    public function getPushParticipationMessage()
    {
        return $this->getValue(self::PUSH_PARTICIPATION_MESSAGE);
    }

    public function getPushParticipationInternalPath()
    {
        return $this->getValue(self::PUSH_PARTICIPATION_INTERNAL_PATH);
    }

    public function getEmailEnabled()
    {
        return $this->getValue(self::EMAIL_ENABLED);
    }

    public function getEmailIdentity()
    {
        return $this->getValue(self::EMAIL_IDENTITY);
    }

    public function getEmailTemplate()
    {
        return $this->getValue(self::EMAIL_TEMPLATE);
    }

    public function getScratchedPercentage()
    {
        return $this->getValue(self::SCRATCHED_PERCENTAGE);
    }

    public function getScratchStart()
    {
        return $this->getValue(self::SCRATCH_START);
    }

    public function getScratchEnd()
    {
        return $this->getValue(self::SCRATCH_END);
    }

    public function isScratchAvailable(): bool
    {
        try {
            $timezone = new DateTimeZone('Atlantic/Canary');
            $today = new DateTime('now', $timezone);

            $fromDate = new DateTime($this->getScratchStart() ?: "");
            $toDate = new DateTime($this->getScratchEnd() ?: "");
            date_time_set($toDate, 23, 59, 59);

            if ($fromDate <= $today && $toDate >= $today) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getTextModalScratch()
    {
        return $this->getValue(self::TEXT_MODAL_SCRATCH);
    }

    public function getTitleModalScratch()
    {
        return $this->getValue(self::TITLE_MODAL_SCRATCH);
    }

    public function getHomaiPromotionIds()
    {
        return explode(',', $this->getValue(self::HOMAI_PROMOTION_IDS) ?: "");
    }

    public function getHomaiPrizeText()
    {
        return $this->getValue(self::HOMAI_PRIZE_TEXT);
    }

    private function getValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }
}
