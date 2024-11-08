<?php

namespace Hiperdino\Anniversary2020\Controller\Success;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected PageFactory $pageFactory;
    protected Session $customerSession;
    protected Config $helperConfig;
    protected CustomerManager $customerManagement;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        Config $helperConfig,
        CustomerManager $customerManagement
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
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

        if (!$this->customerManagement->customerCanParticipate($this->customerSession->getCustomer())) {
            $resultRedirect->setPath($this->helperConfig->getUrlCmsAnniversary());

            return $resultRedirect;
        }

        return $this->pageFactory->create();
    }
}
