<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Package;

use Exception;
use Hiperdino\Dinitos\Model\Data\PackageFactory;
use Hiperdino\Dinitos\Model\Package\Manager;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{
    public function __construct(
        protected PackageFactory $packageFactory,
        protected PackageRepository $packageRepository,
        protected DataPersistorInterface $dataPersistor,
        protected Manager $packageService,
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        try {
            $package = $this->packageRepository->getById($data['id']);
        } catch (Exception $e) {
            $data['id'] = null;
            $package = $this->packageFactory->create();
        }
        try {
            $data['expiration_date'] = $data['expiration_date'] ?? $this->packageService->getExpirationDateFromToday();
            $package->setData($data);
            $this->packageRepository->save($package);
            $this->getMessageManager()->addSuccessMessage(__("Se ha guardado el paquete"));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('dinitos/package/index');
    }
}
