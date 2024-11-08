<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\History;

use Exception;
use Hiperdino\Dinitos\Model\Repository\HistoryRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    protected $dataPersistor;
    protected $dateFilter;
    protected $historyRepository;

    public function __construct(
        Context $context,
        HistoryRepository $historyRepository
    ) {
        $this->historyRepository = $historyRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $alertId = $this->getRequest()->getParam('id', 0);

        try {
            $transaction = $this->historyRepository->getById($alertId);
            $this->historyRepository->delete($transaction);
            $this->getMessageManager()->addSuccessMessage(__("Se ha eliminado el movimiento"));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('dinitos/history/index');
    }
}
