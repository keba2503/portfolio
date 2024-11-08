<?php

namespace Hiperdino\Dinitos\Controller\Xhr;

use Exception;
use Hiperdino\Dinitos\Model\Rewards\Manager;
use Hiperdino\Dinitos\Model\Services\Rewards\GetQuote;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteRepository;
use Singular\EcommerceApp\Helper\Cart;

class ProccessReward extends Action
{
    public function __construct(
        Context $context,
        protected readonly GetQuote $customerRewards,
        protected readonly JsonFactory $resultJsonFactory,
        protected readonly Session $customerSession,
        protected QuoteRepository $quoteRepository,
        protected Cart $cartHelper,
        protected Manager $rewardManager
    ) {
        parent::__construct($context);
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if (!$this->customerSession->isLoggedIn()) {
            return $resultJson->setData(['success' => -1]);
        }
        $selectedRewards = $this->getRequest()->getParam('rewardsResponse');
        try {
            $this->rewardManager->manageRewards($selectedRewards['reward']);
        } catch (Exception $e) {
            return $resultJson->setData(['success' => -1, 'message' => $e->getMessage()]);
        }
        $resultJson->setData(['success' => 1]);

        return $resultJson;
    }
}