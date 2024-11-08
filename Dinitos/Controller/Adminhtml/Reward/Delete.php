<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Reward;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    protected RewardRepository $rewardRepository;
    protected Logger $logger;

    public function __construct(
        RewardRepository $rewardRepository,
        Logger $logger,
        Context $context
    ) {
        $this->rewardRepository = $rewardRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $rewardId = $this->getRequest()->getParam('id');
            $this->rewardRepository->deleteById($rewardId);
            $this->getMessageManager()->addSuccessMessage(__("Se ha eliminado la recompensa"));
        } catch (Exception $e) {
            $this->logger->logDinitosReward("Error eliminando recompensa \n  {$e->getMessage()}");
            $this->getMessageManager()->addErrorMessage(__("No se ha podido eliminar la recompensa"));
        }

        return $redirect->setPath('dinitos/reward/index');
    }
}