<?php

namespace Hiperdino\Anniversary2020\Model\Data;

use Hiperdino\Anniversary2020\Api\Data\ConfigInterface;
use Magento\Framework\Api\AbstractExtensibleObject as AbstractExtensibleObject;

class Config extends AbstractExtensibleObject implements ConfigInterface
{
    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return string
     */
    public function getUrlPageTermsPromotion()
    {
        return $this->_get(self::URL_PAGE_TERMS_PROMOTION);
    }

    /**
     * @param string $urlPageTermsPromotion
     * @return $this
     */
    public function setUrlPageTermsPromotion($urlPageTermsPromotion)
    {
        return $this->setData(self::URL_PAGE_TERMS_PROMOTION, $urlPageTermsPromotion);
    }

    /**
     * @return string
     */
    public function getUrlPageTermsPromotion2()
    {
        return $this->_get(self::URL_PAGE_TERMS_PROMOTION2);
    }

    /**
     * @param string $urlPageTermsPromotion2
     * @return $this
     */
    public function setUrlPageTermsPromotion2($urlPageTermsPromotion2)
    {
        return $this->setData(self::URL_PAGE_TERMS_PROMOTION2, $urlPageTermsPromotion2);
    }

    /**
     * @return string
     */
    public function getTextEmailPromotion()
    {
        return $this->_get(self::TEXT_EMAIL_PROMOTION);
    }

    /**
     * @param string $textEmailPromotion
     * @return $this
     */
    public function setTextEmailPromotion($textEmailPromotion)
    {
        return $this->setData(self::TEXT_EMAIL_PROMOTION, $textEmailPromotion);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getTextPromoHeader()
    {
        return $this->_get(self::TEXT_PROMO_HEADER);
    }

    /**
     * @param string $textPromoHeader
     * @return $this
     */
    public function setTextPromoHeader($textPromoHeader)
    {
        return $this->setData(self::TEXT_PROMO_HEADER, $textPromoHeader);
    }

    /**
     * @return string
     */
    public function getTextPromoFooter()
    {
        return $this->_get(self::TEXT_PROMO_FOOTER);
    }

    /**
     * @param string $textPromoFooter
     * @return $this
     */
    public function setTextPromoFooter($textPromoFooter)
    {
        return $this->setData(self::TEXT_PROMO_FOOTER, $textPromoFooter);
    }

    /**
     * @return string
     */
    public function getImagePromo()
    {
        return $this->_get(self::IMAGE_PROMO);
    }

    /**
     * @param string $imagePromo
     * @return $this
     */
    public function setImagePromo($imagePromo)
    {
        return $this->setData(self::IMAGE_PROMO, $imagePromo);
    }

    /**
     * @return string
     */
    public function getTextTermsPromotion()
    {
        return $this->_get(self::TEXT_TERMS_PROMOTION);
    }

    /**
     * @param string $textTermsPromotion
     * @return $this
     */
    public function setTextTermsPromotion($textTermsPromotion)
    {
        return $this->setData(self::TEXT_TERMS_PROMOTION, $textTermsPromotion);
    }

    /**
     * @return string
     */
    public function getTermsAccept()
    {
        return $this->_get(self::TERMS_ACCEPT);
    }

    /**
     * @param string $termsAccept
     * @return $this
     */
    public function setTermsAccept($termsAccept)
    {
        return $this->setData(self::TERMS_ACCEPT, $termsAccept);
    }

    /**
     * @return string
     */
    public function getTitleWeeklyPromotion()
    {
        return $this->_get(self::TITLE_WEEKLY_PROMOTION);
    }

    /**
     * @param string $titleWeeklyPromotion
     * @return $this
     */
    public function setTitleWeeklyPromotion($titleWeeklyPromotion)
    {
        return $this->setData(self::TITLE_WEEKLY_PROMOTION, $titleWeeklyPromotion);
    }

    /**
     * @return string
     */
    public function getTextWeeklyPromotionHeader()
    {
        return $this->_get(self::TEXT_WEEKLY_PROMOTION_HEADER);
    }

    /**
     * @param string $textWeeklyPromotionHeader
     * @return $this
     */
    public function setTextWeeklyPromotionHeader($textWeeklyPromotionHeader)
    {
        return $this->setData(self::TEXT_WEEKLY_PROMOTION_HEADER, $textWeeklyPromotionHeader);
    }

    /**
     * @return string
     */
    public function getTextWeeklyPromotionFooter()
    {
        return $this->_get(self::TEXT_WEEKLY_PROMOTION_FOOTER);
    }

    /**
     * @param string $textWeeklyPromotionFooter
     * @return $this
     */
    public function setTextWeeklyPromotionFooter($textWeeklyPromotionFooter)
    {
        return $this->setData(self::TEXT_WEEKLY_PROMOTION_FOOTER, $textWeeklyPromotionFooter);
    }

    /**
     * @return string
     */
    public function getImageWeeklyPromotion()
    {
        return $this->_get(self::IMAGE_WEEKLY_PROMOTION);
    }

    /**
     * @param string $imageWeeklyPromotion
     * @return $this
     */
    public function setImageWeeklyPromotion($imageWeeklyPromotion)
    {
        return $this->setData(self::IMAGE_WEEKLY_PROMOTION, $imageWeeklyPromotion);
    }

    /**
     * @return \Singular\EcommerceApp\Api\Data\ShopIslandInterface[]
     */
    public function getIslands()
    {
        return $this->_get(self::ISLANDS);
    }

    /**
     * @param \Singular\EcommerceApp\Api\Data\ShopIslandInterface[] $islands
     * @return $this
     */
    public function setIslands($islands)
    {
        return $this->setData(self::ISLANDS, $islands);
    }

    /**
     * @return string
     */
    public function getTextCardNotRaffle()
    {
        return $this->_get(self::TEXT_CARD_NOT_RAFFLE);
    }

    /**
     * @param string $textCardNotRaffle
     * @return $this
     */
    public function setTextCardNotRaffle($textCardNotRaffle)
    {
        return $this->setData(self::TEXT_CARD_NOT_RAFFLE, $textCardNotRaffle);
    }

    /**
     * @return string
     */
    public function getFinalPromotionTitle()
    {
        return $this->_get(self::FINAL_PROMOTION_TITLE);
    }

    /**
     * @param string $finalPromotionTitle
     * @return $this
     */
    public function setFinalPromotionTitle($finalPromotionTitle)
    {
        return $this->setData(self::FINAL_PROMOTION_TITLE, $finalPromotionTitle);
    }

    /**
     * @return string
     */
    public function getMessageOutPromotion()
    {
        return $this->_get(self::MESSAGE_OUT_PROMOTION);
    }

    /**
     * @param string $messageOutPromotion
     * @return $this
     */
    public function setMessageOutPromotion($messageOutPromotion)
    {
        return $this->setData(self::MESSAGE_OUT_PROMOTION, $messageOutPromotion);
    }

    /**
     * @return string
     */
    public function getTextCardRaffle()
    {
        return $this->_get(self::TEXT_CARD_RAFFLE);
    }

    /**
     * @param string $textCardRaffle
     * @return $this
     */
    public function setTextCardRaffle($textCardRaffle)
    {
        return $this->setData(self::TEXT_CARD_RAFFLE, $textCardRaffle);
    }

    /**
     * @return string
     */
    public function getTextBoton()
    {
        return $this->_get(self::TEXT_BOTON);
    }

    /**
     * @param string $textBoton
     * @return $this
     */
    public function setTextBoton($textBoton)
    {
        return $this->setData(self::TEXT_BOTON, $textBoton);
    }

    /**
     * @return string
     */
    public function getTextBotonScratch()
    {
        return $this->_get(self::TEXT_BOTON_SCRATCH);
    }

    /**
     * @param string $textBotonScratch
     * @return $this
     */
    public function setTextBotonScratch($textBotonScratch)
    {
        return $this->setData(self::TEXT_BOTON_SCRATCH, $textBotonScratch);
    }

    /**
     * @return string
     */
    public function getTextTitleCarrousel()
    {
        return $this->_get(self::TEXT_TITLE_CARROUSEL);
    }

    /**
     * @param string $textTitleCarrousel
     * @return $this
     */
    public function setTextTitleCarrousel($textTitleCarrousel)
    {
        return $this->setData(self::TEXT_TITLE_CARROUSEL, $textTitleCarrousel);
    }

    /**
     * @return string
     */
    public function getImageSectionCarrousel()
    {
        return $this->_get(self::IMAGE_SECTION_CARROUSEL);
    }

    /**
     * @param string $imageSectionCarrousel
     * @return $this
     */
    public function setImageSectionCarrousel($imageSectionCarrousel)
    {
        return $this->setData(self::IMAGE_SECTION_CARROUSEL, $imageSectionCarrousel);
    }

    /**
     * @return string
     */
    public function getTextModalPrize()
    {
        return $this->_get(self::TEXT_MODAL_PRIZE);
    }

    /**
     * @param string $textModalPrize
     * @return $this
     */
    public function setTextModalPrize($textModalPrize)
    {
        return $this->setData(self::TEXT_MODAL_PRIZE, $textModalPrize);
    }

    /**
     * @return string
     */
    public function getBackgroundModal()
    {
        return $this->_get(self::BACKGROUND_MODAL);
    }

    /**
     * @param string $backgroundModal
     * @return $this
     */
    public function setBackgroundModal($backgroundModal)
    {
        return $this->setData(self::BACKGROUND_MODAL, $backgroundModal);
    }

    /**
     * @return string
     */
    public function getImageParticipationWithoutPrize()
    {
        return $this->_get(self::IMAGE_PARTICIPATION_WITHOUT_PRIZE);
    }

    /**
     * @param string $imageParticipationNotPrize
     * @return $this
     */
    public function setImageParticipationWithoutPrize($imageParticipationNotPrize)
    {
        return $this->setData(self::IMAGE_PARTICIPATION_WITHOUT_PRIZE, $imageParticipationNotPrize);
    }

    /**
     * @return string
     */
    public function getTitleRegisterParticipation()
    {
        return $this->_get(self::TITLE_REGISTER_PARTICIPATION);
    }

    /**
     * @param string $titleRegisterParticipation
     * @return $this
     */
    public function setTitleRegisterParticipation($titleRegisterParticipation)
    {
        return $this->setData(self::TITLE_REGISTER_PARTICIPATION, $titleRegisterParticipation);
    }

    /**
     * @return string
     */
    public function getTextConfirmRegister()
    {
        return $this->_get(self::TEXT_CONFIRM_REGISTER);
    }

    /**
     * @param string $textConfirmRegister
     * @return $this
     */
    public function setTextConfirmRegister($textConfirmRegister)
    {
        return $this->setData(self::TEXT_CONFIRM_REGISTER, $textConfirmRegister);
    }

    /**
     * @return string
     */
    public function getImageCodeParticipation()
    {
        return $this->_get(self::IMAGE_CODE_PARTICIPATION);
    }

    /**
     * @param string $imageCodeParticipation
     * @return $this
     */
    public function setImageCodeParticipation($imageCodeParticipation)
    {
        return $this->setData(self::IMAGE_CODE_PARTICIPATION, $imageCodeParticipation);
    }

    /**
     * @return string
     */
    public function getImageScratch()
    {
        return $this->_get(self::IMAGE_SCRATCH);
    }

    /**
     * @param string $imageScratch
     * @return $this
     */
    public function setImageScratch($imageScratch)
    {
        return $this->setData(self::IMAGE_SCRATCH, $imageScratch);
    }

    /**
     * @return string
     */
    public function getScratchedPercentage()
    {
        return $this->_get(self::SCRATCHED_PERCENTAGE);
    }

    /**
     * @param string $scratchedPercentage
     * @return $this
     */
    public function setScratchedPercentage($scratchedPercentage)
    {
        return $this->setData(self::SCRATCHED_PERCENTAGE, $scratchedPercentage);
    }

    /**
     * @return string
     */
    public function getTextModalScratch()
    {
        return $this->_get(self::TEXT_MODAL_SCRATCH);
    }

    /**
     * @param string $textModalScratch
     * @return $this
     */
    public function setTextModalScratch($textModalScratch)
    {
        return $this->setData(self::TEXT_MODAL_SCRATCH, $textModalScratch);
    }

    /**
     * @return string
     */
    public function getTitleModalScratch()
    {
        return $this->_get(self::TITLE_MODAL_SCRATCH);
    }

    /**
     * @param string $titleModalScratch
     * @return $this
     */
    public function setTitleModalScratch($titleModalScratch)
    {
        return $this->setData(self::TITLE_MODAL_SCRATCH, $titleModalScratch);
    }

    /**
     * @return string
     */
    public function getUrlCmsParticipate()
    {
        return $this->_get(self::URL_CMS_PARTICIPATE);
    }

    /**
     * @param string $urlCmsParticipate
     * @return $this
     */
    public function setUrlCmsParticipate($urlCmsParticipate)
    {
        return $this->setData(self::URL_CMS_PARTICIPATE, $urlCmsParticipate);
    }

    /**
     * @return string
     */
    public function getUrlCmsRaffles()
    {
        return $this->_get(self::URL_CMS_RAFFLES);
    }

    /**
     * @param string $urlCmsRaffles
     * @return $this
     */
    public function setUrlCmsRaffles($urlCmsRaffles)
    {
        return $this->setData(self::URL_CMS_RAFFLES, $urlCmsRaffles);
    }

    /**
     * @return string
     */
    public function getUrlCmsDirectAwards()
    {
        return $this->_get(self::URL_CMS_DIRECT_AWARDS);
    }

    /**
     * @param string $urlCmsDirectAwards
     * @return $this
     */
    public function setUrlCmsDirectAwards($urlCmsDirectAwards)
    {
        return $this->setData(self::URL_CMS_DIRECT_AWARDS, $urlCmsDirectAwards);
    }

    /**
     * @return string
     */
    public function getUrlCmsLegal()
    {
        return $this->_get(self::URL_CMS_LEGAL);
    }

    /**
     * @param string $urlCmsLegal
     * @return $this
     */
    public function setUrlCmsLegal($urlCmsLegal)
    {
        return $this->setData(self::URL_CMS_LEGAL, $urlCmsLegal);
    }
}
