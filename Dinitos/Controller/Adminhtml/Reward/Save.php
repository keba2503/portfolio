<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Reward;

use Exception;
use Hiperdino\Dinitos\Model\Data\RewardFactory;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{
    protected DataPersistorInterface $dataPersistor;
    protected RewardFactory $rewardFactory;
    protected RewardRepository $rewardRepository;

    public function __construct(
        RewardFactory $rewardFactory,
        RewardRepository $rewardRepository,
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->rewardFactory = $rewardFactory;
        $this->rewardRepository = $rewardRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        try {
            $dinitosReward = $this->rewardRepository->getById($data['id']);
        } catch (Exception) {
            $data['id'] = null;
            $dinitosReward = $this->rewardFactory->create();
        }

        try {
            $dinitosReward->setData($data);
            $this->rewardRepository->save($dinitosReward);
            $this->getMessageManager()->addSuccessMessage(__("Se ha guardado la recompensa"));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('dinitos/reward/index');
    }
}
