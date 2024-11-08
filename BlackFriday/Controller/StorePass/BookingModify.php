<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;

class BookingModify extends Action
{

    protected $_customerSession;
    protected $_bfStorePassHelper;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper
    ) {
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

        if($this->_bfStorePassHelper->customerAlreadyHasBooking($this->_customerSession->getId())) {
            $this->_customerSession->setData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING, 1);
        }

        $resultRedirect->setPath('hiperdino_blackfriday/storepass');
        return $resultRedirect;
    }
}
