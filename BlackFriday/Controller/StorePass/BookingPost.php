<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;
use Hiperdino\BlackFriday\Model\StorepassTimeslotRepository;

class BookingPost extends Action
{

    protected $_customerSession;
    protected $_bfStorePassHelper;
    protected $_timeslotRepository;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper,
        StorepassTimeslotRepository $timeslotRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->_bfStorePassHelper = $bfStorePassHelper;
        $this->_timeslotRepository = $timeslotRepository;
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
            $timeslotId = $this->getRequest()->getParam('timeslot_id', 0);
            $bookFor = $this->getRequest()->getParam('book_for', '');
            if(!$timeslotId || !$bookFor) {
                throw new \Exception(__('error!'));
            }
            /** @var \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot */
            $timeslot = $this->_timeslotRepository->getById($timeslotId);
            $bookedFor = $this->_convertDateFormat($bookFor);
            $isModifying = (bool) $this->_customerSession->getData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING);
            $this->_bfStorePassHelper->createBooking($customerId, $timeslot, $bookedFor, $isModifying);
            $this->_customerSession->setData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING, 0);
            $this->messageManager->addSuccessMessage(__('Reserva realizada con éxito.'));
            $resultRedirect->setPath('hiperdino_blackfriday/storepass/success');
        } catch(\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('hiperdino_blackfriday/storepass');
        }

        return $resultRedirect;
    }

    /**
     * @param string $date
     * @return string
     */
    protected function _convertDateFormat($date)
    {
        $day = substr($date, 0, 2);
        $month = substr($date, 2, 2);
        $year = substr($date, 4);
        return "{$year}-{$month}-{$day}";
    }
}
