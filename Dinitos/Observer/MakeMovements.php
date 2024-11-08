<?php

namespace Hiperdino\Dinitos\Observer;

use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Model\Services\Orders\AfterNewOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class MakeMovements implements ObserverInterface
{
    public function __construct(
        protected AfterNewOrder $afterNewOrder,
        protected Config $configHelper
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $storeId = $order->getStoreId();

        if ($this->configHelper->isDinitosAccumulatedEnabledWeb($storeId)) {
            $originalState = $order->getOrigData('state');
            $actualState = $order->getState();

            if ($actualState == Order::STATE_PROCESSING && $actualState !== $originalState) {
                $this->afterNewOrder->makeMovements($order);
            }

            return $this;
        }

        return $this;
    }
}
