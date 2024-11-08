<?php

namespace Hiperdino\Anniversary2020\Controller\Adminhtml\Prizes;

use Hiperdino\Anniversary2020\Model\Service\GetCustomerPrizes;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory = false;
    protected Session $session;
    protected GetCustomerPrizes $getCustomerPrizes;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $session,
        GetCustomerPrizes $getCustomerPrizes
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->getCustomerPrizes = $getCustomerPrizes;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Aniversario - Premios')));
        $this->session->setCustomerParticipations([]);

        if ($customerId = $this->_request->getParam('customer_id')) {
            $this->session->setCustomerParticipations($this->getCustomerPrizes->execute($customerId));
        }

        return $resultPage;
    }
}