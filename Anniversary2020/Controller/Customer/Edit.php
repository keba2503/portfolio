<?php

namespace Hiperdino\Anniversary2020\Controller\Customer;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    protected PageFactory $pageFactory;
    protected Session $customerSession;
    protected RegisterParticipation $registerParticipation;
    protected Config $helperConfig;
    protected CustomerManager $customerManagement;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        RegisterParticipation $registerParticipation,
        Config $helperConfig,
        CustomerManager $customerManagement
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->registerParticipation = $registerParticipation;
        $this->helperConfig = $helperConfig;
        $this->customerManagement = $customerManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn() || !$this->helperConfig->isPromotionAvailable()) {
            $resultRedirect->setPath('/');

            return $resultRedirect;
        }

        $rascaCode = trim($this->customerSession->getData(RegisterParticipation::SESSION_RASCA_NAME) ?: "");
        if (!$rascaCode) {
            $resultRedirect->setPath($this->helperConfig->getUrlCmsAnniversary());

            return $resultRedirect;
        }

        if ($this->customerManagement->customerCanParticipate($this->customerSession->getCustomer())) {
            $resultRedirect->setPath($this->helperConfig->getUrlCmsAnniversary());

            return $resultRedirect;
        }

        return $this->pageFactory->create();
    }
}
