<?php

namespace Hiperdino\TimeslotRateException\Controller\Adminhtml\Rate;

use Exception;
use Hiperdino\TimeslotRateException\Model\Repository\RateRepository;
use Hiperdino\TimeslotRateException\Model\ResourceModel\Exception\Collection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    protected $dataPersistor;
    protected $dateFilter;
    protected RateRepository $rateRepository;
    protected Collection $exceptionCollection;

    public function __construct(
        Context $context,
        RateRepository $exceptionRepository,
        Collection $exceptionCollection
    ) {
        $this->rateRepository = $exceptionRepository;
        parent::__construct($context);
        $this->exceptionCollection = $exceptionCollection;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $deliveryRateId = $this->getRequest()->getParam('id', 0);

        try {
            $deliveryRate = $this->rateRepository->getById($deliveryRateId);
            $exceptions = $this->exceptionCollection->addFieldToFilter(
                'rate', ['in' => $deliveryRate->getData('id')]
            )->getItems();
            $exceptionIds = [];
            if (!empty($exceptions)) {
                foreach ($exceptions as $exception) {
                    $exceptionIds[] = $exception->getId();
                }
                $this->getMessageManager()->addErrorMessage(__("No se puede eliminar la tarifa, debido a que tiene las siguientes excepciones asociadas: ") . implode(' , ', $exceptionIds));
            } else {
                $this->rateRepository->delete($deliveryRate);
                $this->getMessageManager()->addSuccessMessage(__("Se ha eliminado la tarifa"));
            }

        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('timeslotrateexception/rate/index');
    }
}
