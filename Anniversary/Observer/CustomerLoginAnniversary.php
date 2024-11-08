<?php

namespace Hiperdino\Anniversary\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLoginAnniversary implements ObserverInterface
{
    protected Session $checkoutSession;

    public function __construct
    (
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->checkoutSession->setLoginCart(true);

        return $this;
    }
}
