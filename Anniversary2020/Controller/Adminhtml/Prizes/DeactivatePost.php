<?php

namespace Hiperdino\Anniversary2020\Controller\Adminhtml\Prizes;

use Hiperdino\Homai\Helper\Services;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class DeactivatePost extends Action
{
    protected Services $homaiServices;

    public function __construct(
        Context $context,
        Services $homaiServices
    ) {
        parent::__construct($context);
        $this->homaiServices = $homaiServices;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $couponCode = $this->_request->getParam('coupon_code');
        $couponDeactivated = $this->homaiServices->deactivateCoupon($couponCode);
        if ($couponDeactivated) {
            $this->messageManager->addSuccessMessage(__("Se ha desactivado el cupón"));
        } else {
            $this->messageManager->addErrorMessage(__("No se ha podido desactivar el cupón"));
        }
        $resultRedirect->setPath('*/*/', ['customer_id' => $this->_request->getParam('customer_id')]);

        return $resultRedirect;
    }
}