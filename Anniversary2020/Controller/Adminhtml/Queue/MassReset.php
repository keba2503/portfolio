<?php

namespace Hiperdino\Anniversary2020\Controller\Adminhtml\Queue;

use Hiperdino\Anniversary2020\Helper\Queue;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class MassReset extends Action
{
    const ERROR_STATUS = 2;

    protected bool|PageFactory $resultPageFactory = false;
    protected Queue $queueHelper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Queue $queueHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->queueHelper = $queueHelper;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $queueIds = $this->getRequest()->getParam("selected", []);

            foreach ($queueIds as $queueId) {

                if ($queueId === null) {
                    $this->getMessageManager()->addErrorMessage(_("El id {$queueId} es incorrecto"));
                    continue;
                }

                $participation = $this->queueHelper->getParticipation($queueId);

                if ($participation['status'] != self::ERROR_STATUS) {
                    $this->getMessageManager()->addErrorMessage(_("La participación {$queueId} que intenta reprocesar no está en estado error, está completa o aún no se ha procesado"));
                    continue;
                }

                $this->queueHelper->markAsPending($queueId, Queue::REQUEST_PARTICIPATION);
            }

            $this->getMessageManager()->addSuccessMessage("Se han marcado las participaciones como pendientes, la cola las procesará cuando pueda.");

        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
