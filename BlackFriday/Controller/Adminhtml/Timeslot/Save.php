<?php

namespace Hiperdino\BlackFriday\Controller\Adminhtml\Timeslot;

use Exception;
use Hiperdino\BlackFriday\Model\StorepassTimeslotFactory;
use Hiperdino\BlackFriday\Model\StorepassTimeslotRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{
    protected $dataPersistor;
    protected $timeslotFactory;
    protected $timeslotRepository;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        StorepassTimeslotFactory $timeslotFactory,
        StorepassTimeslotRepository $timeslotRepository
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->timeslotRepository = $timeslotRepository;
        $this->timeslotFactory = $timeslotFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        try {
            $timeslot = $this->timeslotRepository->getById($data['id']);
        } catch (Exception $e) {
            $data['id'] = null;
            $timeslot = $this->timeslotFactory->create();
        }

        try {
            $timeslot->setData($data);
            $timeslotObject = $this->timeslotRepository->save($timeslot);
            $this->getMessageManager()->addSuccessMessage(__("Se ha guardado la franja horaria" . ($timeslotObject->getName() ? " \"{$timeslotObject->getName()}\"" : '')));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('hiperdino_blackfriday/timeslot/index');
    }
}
