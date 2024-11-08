<?php

namespace Hiperdino\Anniversary2020\Block\Widget;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class RegisterRasca extends Template implements BlockInterface
{
    protected RegisterParticipation $registerParticipation;
    protected Config $helperConfig;
    protected Session $customerSession;
    protected $customer;
    protected CustomerManager $customerManagement;

    public function __construct(
        RegisterParticipation $registerParticipation,
        Config $helperConfig,
        Session $customerSession,
        Template\Context $context,
        CustomerManager $customerManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('Hiperdino_Anniversary2020::widget/registerrasca.phtml');
        $this->registerParticipation = $registerParticipation;
        $this->helperConfig = $helperConfig;
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
    }

    public function isPromotionAvailable()
    {
        return $this->helperConfig->isPromotionAvailable();
    }

    public function getTitle()
    {
        return $this->helperConfig->getRegisterRascaTitle();
    }

    public function getTextOutPromo()
    {
        return $this->helperConfig->getRegisterRascaTextOutPromo();
    }

    public function getTextPromoHeader()
    {
        return $this->helperConfig->getRegisterRascaTextPromoHeader();
    }

    public function getTextPromoFooter()
    {
        return $this->helperConfig->getRegisterRascaTextPromoFooter();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getImagePromo()
    {
        return $this->helperConfig->getRegisterRascaImagePromo();
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getNumCharactersRasca()
    {
        return $this->helperConfig->getNumCharactersRasca();
    }

    public function getAjaxActionUrl()
    {
        return $this->getUrl('hdanniversary/ajax/registerRasca');
    }

    public function getCodesUrl()
    {
        return $this->getUrl('hdanniversary/codes');
    }

    /**
     * @return string
     */
    public function getCurrentRascaCodeFromSession()
    {
        return (string)$this->customerSession->getData(RegisterParticipation::SESSION_RASCA_NAME);
    }

    /**
     * @return string
     */
    public function getCustomerRascasHistoryHtml()
    {
        if (!$this->customerSession->isLoggedIn()) return '';
        $message = $this->customerManagement->getCustomerRascasHistoryMsg($this->customerSession->getId());

        return "<div class='rasca-customer-history'>{$message}</div>";
    }

    public function getTitleWeek()
    {
        return $this->helperConfig->getTitleWeeklyPromotion();
    }

    public function getFinalTitle()
    {
        return $this->helperConfig->getFinalPromotionTitle();
    }

    public function getRascaSessionError()
    {
        $rascaError = (string)$this->customerSession->getData(RegisterParticipation::SESSION_ERROR_NAME);
        if ($rascaError) {
            $this->customerSession->unsetData(RegisterParticipation::SESSION_ERROR_NAME);

            return $rascaError;
        }

        return false;
    }

    public function getCodeSession()
    {
        $codeNumber = $this->customerSession->getData(RegisterParticipation::SESSION_RASCA_NAME);
        $this->customerSession->unsetData(RegisterParticipation::SESSION_RASCA_NAME);

        return $codeNumber;
    }

    public function getRegisterTitle()
    {
        return $this->helperConfig->getTitleRegisterParticipation();
    }

    public function getRegisterImage()
    {
        return $this->helperConfig->getRegisterRascaImageCode();
    }


}
