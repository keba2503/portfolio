<?php

namespace Hiperdino\Anniversary2020\Observer;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Participation\ManagerRequestParticipation;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RequestParticipation implements ObserverInterface
{
    protected ManagerRequestParticipation $managerRequestParticipation;
    private Config $config;

    public function __construct(
        ManagerRequestParticipation $managerRequestParticipation,
        Config $config
    ) {
        $this->managerRequestParticipation = $managerRequestParticipation;
        $this->config = $config;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $participationQty = $order->getData('anniversary_total_qty');

        if ($this->config->isPromotionAvailable() & $this->config->isAnniversaryEnabled() & $participationQty > 0) {
            $this->managerRequestParticipation->execute($order);
        }
    }
}
