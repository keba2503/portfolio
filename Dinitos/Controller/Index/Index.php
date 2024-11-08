<?php

namespace Hiperdino\Dinitos\Controller\Index;

use Hiperdino\Dinitos\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Index implements ActionInterface
{
    public function __construct(
        protected PageFactory $resultPageFactory,
        protected ResultFactory $resultFactory,
        protected Session $customerSession,
        protected Config $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->customerSession->isLoggedIn() || !$this->config->isDinitosAccumulatedEnabledWeb()) {
            return $redirect->setPath('/');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Mis Dinitos'));

        return $resultPage;
    }
}