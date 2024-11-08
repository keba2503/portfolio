<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;

class Index extends Action
{

    protected $_pageFactory;
    protected $_customerSession;
    protected $_bfStorePassHelper;
    protected $_coreSession;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper,
        SessionManagerInterface $coreSession
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_bfStorePassHelper = $bfStorePassHelper;
        $this->_coreSession = $coreSession;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $isActive = $this->_bfStorePassHelper->isActive();

        if(!$isActive || !$this->_customerSession->isLoggedIn()) {
            $this->_coreSession->setRedirectAfterLogin($this->_url->getUrl('hiperdino_blackfriday/storepass'));
            $resultRedirect->setUrl($this->_bfStorePassHelper->getNoLoginUrl());
            return $resultRedirect;
        }

        $isModifying = (bool) $this->_customerSession->getData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING);
        if(!$isModifying && $this->_bfStorePassHelper->customerAlreadyHasBooking($this->_customerSession->getId())) {
            $resultRedirect->setPath('hiperdino_blackfriday/storepass/success');
            return $resultRedirect;
        }

        $pageFactory = $this->_pageFactory->create();
        $pageFactory->getConfig()->getTitle()->set(__('Black Friday'));

        return $pageFactory;
    }
}
