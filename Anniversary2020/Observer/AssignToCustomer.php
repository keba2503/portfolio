<?php

namespace Hiperdino\Anniversary2020\Observer;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Participation\ManagerAssignToCustomer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AssignToCustomer implements ObserverInterface
{
    protected ManagerAssignToCustomer $managerAssignToCustomer;
    protected Config $config;

    public function __construct(
        ManagerAssignToCustomer $managerAssignToCustomer,
        Config $config
    ) {
        $this->managerAssignToCustomer = $managerAssignToCustomer;
        $this->config = $config;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $digitalTicket = $observer->getEvent()->getData('digital_ticket');
        if ($this->config->isAnniversaryEnabled() & $this->config->isPromotionAvailable()) {
            $this->managerAssignToCustomer->assignToCustomer($digitalTicket);
        }
    }
}
