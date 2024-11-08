<?php

namespace Hiperdino\TimeslotRateException\Controller\Adminhtml\Exception;

use Exception;
use Hiperdino\TimeslotRateException\Model\Repository\ExceptionRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    protected $dataPersistor;
    protected $dateFilter;
    protected ExceptionRepository $exceptionRepository;

    public function __construct(
        Context $context,
        ExceptionRepository $exceptionRepository
    ) {
        $this->exceptionRepository = $exceptionRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $exceptionId = $this->getRequest()->getParam('id', 0);

        try {
            $transaction = $this->exceptionRepository->getById($exceptionId);
            $this->exceptionRepository->delete($transaction);
            $this->getMessageManager()->addSuccessMessage(__("Se ha eliminado la excepcion"));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('timeslotrateexception/exception/index');
    }
}
