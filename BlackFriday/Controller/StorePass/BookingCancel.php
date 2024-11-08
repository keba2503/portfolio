<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;

class BookingCancel extends Action
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

        try {
            $customerId = $this->_customerSession->getId();
            $bookingId = $this->getRequest()->getParam('booking_id', 0);
            $this->_bfStorePassHelper->cancelBookingForCustomer($bookingId, $customerId);
            $this->_customerSession->setData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING, 0);
            $this->messageManager->addSuccessMessage(__('Reserva cancelada con éxito.'));
            $resultRedirect->setPath('/');
        } catch(\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('hiperdino_blackfriday/storepass/success');
        }

        return $resultRedirect;
    }
}
