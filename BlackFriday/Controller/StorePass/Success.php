<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;

class Success extends Action
{

    protected $_pageFactory;
    protected $_customerSession;
    protected $_bfStorePassHelper;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_bfStorePassHelper = $bfStorePassHelper;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $isActive = $this->_bfStorePassHelper->isActive();

        if(!$isActive || !$this->_customerSession->isLoggedIn()) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        if(! $this->_bfStorePassHelper->customerAlreadyHasBooking($this->_customerSession->getId())) {
            $resultRedirect->setPath('hiperdino_blackfriday/storepass');
            return $resultRedirect;
        }

        $pageFactory = $this->_pageFactory->create();
        $pageFactory->getConfig()->getTitle()->set(__('Black Friday'));

        return $pageFactory;
    }
}
