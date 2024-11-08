<?php

namespace Hiperdino\Anniversary2020\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Anniversary2020 config interface.
 * @api
 */
interface ConfigInterface extends CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const IS_ACTIVE = 'is_active';
    const URL_PAGE_TERMS_PROMOTION = 'url_page_terms_promotion';
    const URL_PAGE_TERMS_PROMOTION2 = 'url_page_terms_promotion2';
    const TEXT_EMAIL_PROMOTION = 'text_email_promotion';
    const TITLE = 'title';
    const TEXT_PROMO_HEADER = 'text_promo_header';
    const TEXT_PROMO_FOOTER = 'text_promo_footer';
    const IMAGE_PROMO = 'image_promo';
    const TEXT_TERMS_PROMOTION = 'text_terms_promotion';
    const TERMS_ACCEPT = 'terms_accept';
    const TITLE_WEEKLY_PROMOTION = 'title_weekly_promotion';
    const TEXT_WEEKLY_PROMOTION_HEADER = 'text_weekly_promotion_header';
    const TEXT_WEEKLY_PROMOTION_FOOTER = 'text_weekly_promotion_footer';
    const IMAGE_WEEKLY_PROMOTION = 'image_weekly_promotion';
    const FINAL_PROMOTION_TITLE = 'final_promotion_title';
    const MESSAGE_OUT_PROMOTION = 'message_out_promotion';
    const TEXT_CARD_NOT_RAFFLE = 'text_card_not_raffle';
    const TEXT_CARD_RAFFLE = 'text_card_raffle';
    const TEXT_BOTON = 'text_boton';
    const TEXT_BOTON_SCRATCH = 'text_boton_scratch';
    const TEXT_TITLE_CARROUSEL = 'text_title_carrousel';
    const IMAGE_SECTION_CARROUSEL = 'image_section_carrousel';
    const IMAGE_SCRATCH = 'image_scratch';
    const SCRATCHED_PERCENTAGE = 'scratched_percentage';
    const TEXT_MODAL_SCRATCH = 'text_modal_scratch';
    const TITLE_MODAL_SCRATCH = 'title_modal_scratch';
    const TEXT_MODAL_PRIZE = 'text_modal_prize';
    const BACKGROUND_MODAL = 'background_modal';
    const IMAGE_PARTICIPATION_WITHOUT_PRIZE = 'image_participation_without_prize';
    const TITLE_REGISTER_PARTICIPATION = 'title_register_participation';
    const TEXT_CONFIRM_REGISTER = 'text_confirm_register';
    const ISLANDS = 'islands';
    const IMAGE_CODE_PARTICIPATION = 'image_code_participation';
    const URL_CMS_RAFFLES = 'url_cms_raffles';
    const URL_CMS_DIRECT_AWARDS = 'url_cms_direct_awards';
    const URL_CMS_PARTICIPATE = 'url_cms_participate';
    const URL_CMS_LEGAL = 'url_cms_legal';

    /**
     * @return bool
     */
    public function getIsActive();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getUrlPageTermsPromotion();

    /**
     * @param string $urlPageTermsPromotion
     * @return $this
     */
    public function setUrlPageTermsPromotion($urlPageTermsPromotion);

    /**
     * @return string
     */
    public function getUrlPageTermsPromotion2();

    /**
     * @param string $urlPageTermsPromotion2
     * @return $this
     */
    public function setUrlPageTermsPromotion2($urlPageTermsPromotion2);

    /**
     * @return string
     */
    public function getTextEmailPromotion();

    /**
     * @param string $textEmailPromotion
     * @return $this
     */
    public function setTextEmailPromotion($textEmailPromotion);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTextPromoHeader();

    /**
     * @return string
     */
    public function getFinalPromotionTitle();

    /**
     * @param string $finalPromotionTitle
     * @return $this
     */
    public function setFinalPromotionTitle($finalPromotionTitle);

    /**
     * @return string
     */
    public function getMessageOutPromotion();

    /**
     * @param string $messageOutPromotion
     * @return $this
     */
    public function setMessageOutPromotion($messageOutPromotion);


    /**
     * @param string $textPromoHeader
     * @return $this
     */
    public function setTextPromoHeader($textPromoHeader);

    /**
     * @return string
     */
    public function getTextPromoFooter();

    /**
     * @param string $textPromoFooter
     * @return $this
     */
    public function setTextPromoFooter($textPromoFooter);

    /**
     * @return string
     */
    public function getImagePromo();

    /**
     * @param string $imagePromo
     * @return $this
     */
    public function setImagePromo($imagePromo);

    /**
     * @return string
     */
    public function getTextTermsPromotion();

    /**
     * @param string $textTermsPromotion
     * @return $this
     */
    public function setTextTermsPromotion($textTermsPromotion);

    /**
     * @return string
     */
    public function getTermsAccept();

    /**
     * @param string $termsAccept
     * @return $this
     */
    public function setTermsAccept($termsAccept);

    /**
     * @return string
     */
    public function getTitleWeeklyPromotion();

    /**
     * @param string $titleWeeklyPromotion
     * @return $this
     */
    public function setTitleWeeklyPromotion($titleWeeklyPromotion);

    /**
     * @return string
     */
    public function getTextWeeklyPromotionHeader();

    /**
     * @param string $textWeeklyPromotionHeader
     * @return $this
     */
    public function setTextWeeklyPromotionHeader($textWeeklyPromotionHeader);

    /**
     * @return string
     */
    public function getTextWeeklyPromotionFooter();

    /**
     * @param string $textWeeklyPromotionFooter
     * @return $this
     */
    public function setTextWeeklyPromotionFooter($textWeeklyPromotionFooter);

    /**
     * @return string
     */
    public function getImageWeeklyPromotion();

    /**
     * @param string $imageWeeklyPromotion
     * @return $this
     */
    public function setImageWeeklyPromotion($imageWeeklyPromotion);

    /**
     * @return \Singular\EcommerceApp\Api\Data\IslandInterface[]
     */
    public function getIslands();

    /**
     * @param \Singular\EcommerceApp\Api\Data\IslandInterface[] $islands
     * @return $this
     */
    public function setIslands($islands);

    /**
     * @return string
     */
    public function getTextCardNotRaffle();


    /**
     * @return $this
     */
    public function setTextCardNotRaffle($textCardNotRaffle);

    /**
     * @return string
     */
    public function getTextCardRaffle();

    /**
     * @param string $textCardRaffle
     * @return $this
     */
    public function setTextCardRaffle($textCardRaffle);

    /**
     * @return string
     */
    public function getTextBoton();

    /**
     * @param string $textBoton
     * @return $this
     */
    public function setTextBoton($textBoton);

    /**
     * @return string
     */
    public function getTextBotonScratch();

    /**
     * @param string $textBotonScratch
     * @return $this
     */
    public function setTextBotonScratch($textBotonScratch);

    /**
     * @return string
     */
    public function getTextTitleCarrousel();

    /**
     * @param string $textTitleCarrousel
     * @return $this
     */
    public function setTextTitleCarrousel($textTitleCarrousel);

    /**
     * @return string
     */
    public function getImageSectionCarrousel();

    /**
     * @param string $imageSectionCarrousel
     * @return $this
     */
    public function setImageSectionCarrousel($imageSectionCarrousel);

    /**
     * @return string
     */
    public function getTextModalPrize();

    /**
     * @param string $textModalPrize
     * @return $this
     */
    public function setTextModalPrize($textModalPrize);

    /**
     * @return string
     */
    public function getBackgroundModal();

    /**
     * @param string $backgroundModal
     * @return $this
     */
    public function setBackgroundModal($backgroundModal);

    /**
     * @return string
     */
    public function getImageParticipationWithoutPrize();

    /**
     * @param string $imageParticipationNotPrize
     * @return $this
     */
    public function setImageParticipationWithoutPrize($imageParticipationNotPrize);

    /**
     * @return string
     */
    public function getTitleRegisterParticipation();

    /**
     * @param string $titleRegisterParticipation
     * @return $this
     */
    public function setTitleRegisterParticipation($titleRegisterParticipation);

    /**
     * @return string
     */
    public function getTextConfirmRegister();

    /**
     * @param string $textConfirmRegister
     * @return $this
     */
    public function setTextConfirmRegister($textConfirmRegister);

    /**
     * @return string
     */
    public function getImageCodeParticipation();

    /**
     * @param string $imageCodeParticipation
     * @return $this
     */
    public function setImageCodeParticipation($imageCodeParticipation);

    /**
     * @return string
     */
    public function getImageScratch();

    /**
     * @param string $imageScratch
     * @return $this
     */
    public function setImageScratch($imageScratch);


    /**
     * @return string
     */
    public function getScratchedPercentage();

    /**
     * @param string $scratchedPercentage
     * @return $this
     */
    public function setScratchedPercentage($scratchedPercentage);

    /**
     * @return string
     */
    public function getTextModalScratch();

    /**
     * @param string $textModalScratch
     * @return $this
     */
    public function setTextModalScratch($textModalScratch);

    /**
     * @return string
     */
    public function getTitleModalScratch();

    /**
     * @param string $titleModalScratch
     * @return $this
     */
    public function setTitleModalScratch($titleModalScratch);

    /**
     * @return string
     */
    public function getUrlCmsRaffles();

    /**
     * @param string $urlCmsRaffles
     * @return $this
     */
    public function setUrlCmsRaffles($urlCmsRaffles);

    /**
     * @return string
     */
    public function getUrlCmsDirectAwards();

    /**
     * @param string $urlCmsDirectAwards
     * @return $this
     */
    public function setUrlCmsDirectAwards($urlCmsDirectAwards);

    /**
     * @return string
     */
    public function getUrlCmsParticipate();

    /**
     * @param string $urlCmsParticipate
     * @return $this
     */
    public function setUrlCmsParticipate($urlCmsParticipate);

    /**
     * @return string
     */
    public function getUrlCmsLegal();

    /**
     * @param string $urlCmsLegal
     * @return $this
     */
    public function setUrlCmsLegal($urlCmsLegal);

}