<?php

namespace Hiperdino\TimeslotRateException\Controller\Adminhtml\Exception;

use Exception;
use Hiperdino\TimeslotRateException\Model\Data\Exception as ExceptionTimeslot;
use Hiperdino\TimeslotRateException\Model\Data\ExceptionFactory;
use Hiperdino\TimeslotRateException\Model\Repository\ExceptionRepository;
use Hiperdino\TimeslotRateException\Model\Services\DeliveryTypeService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Save extends Action
{
    protected ExceptionFactory $exceptionFactory;
    protected ExceptionRepository $exceptionRepository;
    protected DeliveryTypeService $deliveryTypeService;

    public function __construct(
        Context $context,
        ExceptionFactory $exceptionFactory,
        ExceptionRepository $exceptionRepository,
        DeliveryTypeService $deliveryTypeService
    ) {
        $this->exceptionRepository = $exceptionRepository;
        $this->exceptionFactory = $exceptionFactory;
        $this->deliveryTypeService = $deliveryTypeService;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data['description']) {
            unset($data['description']);
        }

        try {
            $rateException = $this->exceptionRepository->getById($data['id']);
        } catch (Exception $e) {
            $data['id'] = null;
            $rateException = $this->exceptionFactory->create();
        }

        [$data, $deliveryType, $deliveryStoreViews] = $this->deliveryTypeService->extractDeliveryInfo($data);

        $data = $this->deliveryTypeService->prepareTimeslotsDataFromDeliveryType($deliveryType, $deliveryStoreViews, $data);

        $this->saveExceptionDataWithMessage($rateException, $data);

        return $resultRedirect->setPath('timeslotrateexception/exception/index');
    }

    /**
     * @param ExceptionTimeslot $rateException
     * @param mixed $data
     * @return void
     */
    public function saveExceptionDataWithMessage(ExceptionTimeslot $rateException, $data): void
    {
        try {
            $rateException->setData($data);
            $rateExceptionObject = $this->exceptionRepository->save($rateException);
            $this->getMessageManager()->addSuccessMessage(__("Se ha guardado la excepción" . ($rateExceptionObject->getName() ? " \"{$rateExceptionObject->getName()}\"" : '')));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage("No se ha podido guardar la excepción");
            $this->getMessageManager()->addErrorMessage("El error técnico es: " . $e->getMessage());
        }
    }
}
