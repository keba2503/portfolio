<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Package;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Create extends Action implements ActionInterface
{
    protected bool|PageFactory $resultPageFactory = false;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $title = $this->getRequest()->getParam('id') ?
            __('Dinitos - Editar Paquete') :
            __('Dinitos - Nuevo Paquete');

        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}