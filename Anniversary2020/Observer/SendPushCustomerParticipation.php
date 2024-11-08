<?php

namespace Hiperdino\Anniversary2020\Observer;

use Hiperdino\Anniversary2020\Model\Service\Pusher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SendPushCustomerParticipation implements ObserverInterface
{
    protected Pusher $pusher;

    public function __construct(
        Pusher $pusher
    ) {
        $this->pusher = $pusher;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customerId = $observer->getEvent()->getData('customer_id');

        $this->pusher->sendCustomerParticipationPush($customerId);
    }
}
