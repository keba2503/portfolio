<?php

namespace Hiperdino\Anniversary2020\Controller\Codes;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected PageFactory $pageFactory;
    protected Session $customerSession;
    protected RegisterParticipation $registerParticipation;
    protected Config $anniversaryConfig;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        Config $anniversaryConfig,
        RegisterParticipation $registerParticipation
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
            $resultRedirect->setPath($this->anniversaryConfig->getUrlCmsAnniversary());

            return $resultRedirect;
        }

        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Mis códigos registrados')));

        return $resultPage;
    }
}
