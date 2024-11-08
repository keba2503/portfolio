<?php

namespace Hiperdino\Anniversary2020\Observer;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerRegister implements ObserverInterface
{
    protected Session $customerSession;
    protected Config $helperConfig;
    protected CustomerManager $customerManagement;
    protected Logger $log;

    /**
     * @param Session $customerSession
     * @param Config $helperConfig
     * @param CustomerManager $customerManagement
     * @param Logger $log
     */
    public function __construct(
        Session $customerSession,
        Config $helperConfig,
        CustomerManager $customerManagement,
        Logger $log
    ) {
        $this->customerSession = $customerSession;
        $this->helperConfig = $helperConfig;
        $this->customerManagement = $customerManagement;
        $this->log = $log;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->log->Log("Entra en el observer CustomerRegister para Anniversary2020");

        if (!$this->helperConfig->isPromotionAvailable()) {
            return $this;
        }

        /** @var Customer $customer */
        $customer = $observer->getEvent()->getData('customer');

        $customerAddresses = $customer->getAddresses();
        foreach ($customerAddresses as $customerAddress) {
            $telephone = $customerAddress->getTelephone();
            if ($telephone) {
                $this->customerManagement->setCustomerTelephone($customer, $telephone);
            }
        }

        return $this;
    }
}
