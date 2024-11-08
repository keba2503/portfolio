<?php

namespace Hiperdino\Anniversary2020\Block;

use Hiperdino\Anniversary2020\Block\Widget\RegisterRasca;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;

class Success extends RegisterRasca
{
    public function getTitleWeeklyPromotion()
    {
        return $this->helperConfig->getTitleWeeklyPromotion();
    }

    public function getTextWeeklyPromotionHeader()
    {
        return $this->helperConfig->getTextWeeklyPromotionHeader();
    }

    public function getTextWeeklyPromotionFooter()
    {
        return $this->helperConfig->getTextWeeklyPromotionFooter();
    }

    public function getImageWeeklyPromotion()
    {
        return $this->helperConfig->getImageWeeklyPromotion();
    }

    /**
     * @return bool|string
     */
    public function getRascaSessionError()
    {
        $rascaError = (string)$this->customerSession->getData(RegisterParticipation::SESSION_ERROR_NAME);
        if ($rascaError) {
            $this->customerSession->unsetData(RegisterParticipation::SESSION_ERROR_NAME);

            return $rascaError;
        }

        return false;
    }

    public function getRascaSessionRegistered()
    {
        $rascaRegistered = (bool)$this->customerSession->getData(RegisterParticipation::SESSION_REGISTERED_RASCA);
        if ($rascaRegistered) {
            $this->customerSession->unsetData(RegisterParticipation::SESSION_REGISTERED_RASCA);

            return true;
        }

        return false;
    }

    public function getCodeSession()
    {
        $codeNumber = $this->customerSession->getData(RegisterParticipation::SESSION_RASCA_NAME);
        $this->customerSession->unsetData(RegisterParticipation::SESSION_RASCA_NAME);

        return $codeNumber;

    }

    public function getTextCardNotRaffle()
    {
        return $this->helperConfig->getTextCardNotRaffle();
    }

    public function getTextCardRaffle()
    {
        return $this->helperConfig->getTextCardRaffle();
    }

    public function getTextBoton()
    {
        return $this->helperConfig->getTextBoton();
    }

    public function getTextBotonScratch()
    {
        return $this->helperConfig->getTextBotonScratch();
    }

    public function getTitleCarrousel()
    {
        return $this->helperConfig->getTextTitleCarrousel();
    }

    public function getImageCarrousel()
    {
        return $this->helperConfig->getImageCarrousel();
    }

    public function getTextModalPrize()
    {
        return $this->helperConfig->getTextModalPrize();
    }

    public function getBackgroundModal()
    {
        return $this->helperConfig->getBackgroundModal();
    }

    public function getImageWithoutPrize()
    {
        return $this->helperConfig->getImageWithoutPrize();
    }

    public function getTitleRegisterParticipation()
    {
        return $this->helperConfig->getTitleRegisterParticipation();
    }

    public function getTextConfirmRegister()
    {
        return $this->helperConfig->getTextConfirmRegister();
    }
}
