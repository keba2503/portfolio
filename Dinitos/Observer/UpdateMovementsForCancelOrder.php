<?php

namespace Hiperdino\Dinitos\Observer;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Model\Services\Orders\AfterOrderCancel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class UpdateMovementsForCancelOrder implements ObserverInterface
{
    public function __construct(
        protected AfterOrderCancel $afterOrderCancel,
        protected Config $configHelper
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->configHelper->isDinitosAccumulatedEnabledWeb()) {
            $order = $observer->getEvent()->getData('order');

            $originalState = $order->getOrigData('state');
            $actualState = $order->getState();

            if ($actualState == Order::STATE_CANCELED && $actualState !== $originalState) {
                $this->afterOrderCancel->makeMovements($order);
            }

            return $this;
        }

        return $this;
    }
}
