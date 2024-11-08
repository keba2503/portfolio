<?php

namespace Hiperdino\Anniversary2020\Controller\Rasca;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Participation\ManagerRegisterParticipation;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected PageFactory $pageFactory;
    protected Session $customerSession;
    protected ManagerRegisterParticipation $registerParticipation;
    protected Config $anniversaryConfig;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        Config $anniversaryConfig,
        ManagerRegisterParticipation $registerParticipation
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->registerParticipation = $registerParticipation;
        parent::__construct($context);
        $this->anniversaryConfig = $anniversaryConfig;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $resultRedirect->setPath($this->anniversaryConfig->getUrlCmsAnniversary());
        }

        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Mis códigos registrados')));

        return $resultPage;
    }
}
