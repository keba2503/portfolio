<?php

namespace Hiperdino\TimeslotRateException\Controller\Adminhtml\Rate;

use Exception;
use Hiperdino\TimeslotRateException\Model\Data\RateFactory;
use Hiperdino\TimeslotRateException\Model\Repository\RateRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{
    protected DataPersistorInterface $dataPersistor;
    protected RateFactory $rateFactory;
    protected RateRepository $rateRepository;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        RateFactory $rateFactory,
        RateRepository $rateRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->rateRepository = $rateRepository;
        $this->rateFactory = $rateFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        try {
            $transaction = $this->rateRepository->getById($data['id']);
        } catch (Exception) {
            $data['id'] = null;
            $transaction = $this->rateFactory->create();
        }

        try {
            $transaction->setData($data);
            $this->rateRepository->save($transaction);
            $this->getMessageManager()->addSuccessMessage(__("Se ha guardado la tarifa"));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('timeslotrateexception/rate/index');
    }
}
