<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\History;

use Exception;
use Hiperdino\Dinitos\Model\Repository\HistoryRepository;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\SetDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetTypeMovements;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

class Save extends Action
{
    protected HistoryRepository $historyRepository;
    protected GetTypeMovements $movementManager;
    protected GetDinitos $getCustomerDinitosService;
    protected SetDinitos $setCustomerDinitosService;
    protected CustomerRepository $customerRepository;

    public function __construct(
        Context $context,
        HistoryRepository $historyRepository,
        GetTypeMovements $movementManager,
        GetDinitos $getCustomerDinitosService,
        SetDinitos $setCustomerDinitosService,
        CustomerRepository $customerRepository
    ) {
        $this->historyRepository = $historyRepository;
        parent::__construct($context);
        $this->movementManager = $movementManager;
        $this->getCustomerDinitosService = $getCustomerDinitosService;
        $this->setCustomerDinitosService = $setCustomerDinitosService;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $isPositive = $data['dinitos_quantity'] > 0;
        $customerId = $data['customer_id'];
        try {
            $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage(__("El customer_id no existe."));

            return $resultRedirect->setPath('dinitos/history/index');
        }
        $data['transaction_type'] = $isPositive ? GetTypeMovements::ACCUMULATION_MOVEMENT : GetTypeMovements::REDEMPTION_MOVEMENT;
        $dinitosQty = $data['dinitos_quantity'];
        $customerDinitos = $this->getCustomerDinitosService->getCustomerDinitosTotal($customerId);
        if (!$isPositive && $customerDinitos < abs($dinitosQty)) {
            $this->getMessageManager()->addErrorMessage(__("El cliente no tiene dinitos suficientes para realizar este movimiento."));

            return $resultRedirect->setPath('dinitos/history/index');
        }
        try {
            $movement = $this->movementManager->getMovement($data['transaction_type']);
            $result = $movement->handleAdminMovement($data);
            if ($result) {
                $dinitosBalance = $customerDinitos + $dinitosQty;
                $this->setCustomerDinitosService->setCustomerDinitos($customerId, $dinitosBalance);
                $this->getMessageManager()->addSuccessMessage(__("Se ha guardado el movimiento"));
            }
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('dinitos/history/index');
    }
}
