<?php

namespace Hiperdino\Anniversary2020\Controller\Adminhtml\Post;

use Hiperdino\Anniversary2020\Helper\Config;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Rascas extends Action
{
    protected bool|PageFactory $resultPageFactory = false;
    protected Config $helperConfig;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $helperConfig,

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helperConfig = $helperConfig;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Aniversario 2020 - Rascas')));

        return $resultPage;
    }
}
