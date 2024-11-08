<?php

namespace Hiperdino\Dinitos\Controller\Adminhtml\Package;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    protected PackageRepository $packageRepository;
    protected Logger $logger;

    public function __construct(
        PackageRepository $packageRepository,
        Logger $logger,
        Context $context
    ) {
        $this->packageRepository = $packageRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $packageId = $this->getRequest()->getParam('id');
            $this->packageRepository->deleteById($packageId);
            $this->getMessageManager()->addSuccessMessage(__("Se ha eliminado el paquete"));
        } catch (Exception $e) {
            $this->logger->logPackages("Error eliminando paquete \n  {$e->getMessage()}");
            $this->getMessageManager()->addErrorMessage(__("No se ha podido eliminar el paquete"));
        }

        return $redirect->setPath('dinitos/package/index');
    }
}