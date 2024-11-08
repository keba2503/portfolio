<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Package;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements ActionInterface
{
    public function __construct(
        protected Context $context,
        protected PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Dinitos - Paquetes')));

        return $resultPage;
    }
}