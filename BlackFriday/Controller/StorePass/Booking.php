<?php

namespace Hiperdino\BlackFriday\Controller\StorePass;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Hiperdino\BlackFriday\Helper\StorePass as BlackFridayStorePassHelper;
use Singular\Tiendas\Model\TiendasRepository;

class Booking extends Action
{

    protected $_pageFactory;
    protected $_customerSession;
    protected $_bfStorePassHelper;
    protected $_tiendasRepository;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        BlackFridayStorePassHelper $bfStorePassHelper,
        TiendasRepository $tiendasRepository
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_bfStorePassHelper = $bfStorePassHelper;
        $this->_tiendasRepository = $tiendasRepository;
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

        $isModifying = (bool) $this->_customerSession->getData(BlackFridayStorePassHelper::SESSION_STOREPASS_IS_MODIFYING);
        if(!$isModifying && $this->_bfStorePassHelper->customerAlreadyHasBooking($this->_customerSession->getId())) {
            $resultRedirect->setPath('hiperdino_blackfriday/storepass/success');
            return $resultRedirect;
        }

        $tiendaId = $this->getRequest()->getParam('t', 0);
        try {
            /** @var \Singular\Tiendas\Model\Tiendas $tienda */
            $tienda = $this->_tiendasRepository->getById($tiendaId);
            if(! $tienda->getData('is_black_friday')) {
                throw new \Exception('La tienda no es de tipo Black Friday');
            }
        } catch(\Exception $e) {
            $resultRedirect->setPath('hiperdino_blackfriday/storepass');
            return $resultRedirect;
        }

        $pageFactory = $this->_pageFactory->create();
        $pageFactory->getConfig()->getTitle()->set(__('Black Friday'));

        return $pageFactory;
    }
}
