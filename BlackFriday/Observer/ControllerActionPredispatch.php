<?php

namespace Hiperdino\BlackFriday\Observer;

use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

class ControllerActionPredispatch implements ObserverInterface
{

    protected $_customerSession;
    protected $_bfStorepassHelper;

    public function __construct(
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorepassHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->_bfStorepassHelper = $bfStorepassHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $bfStorepassActive = $this->_bfStorepassHelper->isActive();
        if(! $bfStorepassActive) return;
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getData('request');
        $uriString = $request->getUriString();
        $isAjax = $request->isAjax();
        $isModifying = (bool) $this->_customerSession->getData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING);
        if($isModifying && !$isAjax && strpos($uriString, 'hiperdino_blackfriday/storepass') === false) {
            $this->_customerSession->setData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING, 0);
        }
    }
}
