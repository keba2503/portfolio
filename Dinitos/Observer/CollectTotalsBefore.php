<?php

namespace Hiperdino\Dinitos\Observer;

use Hiperdino\Dinitos\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class CollectTotalsBefore implements ObserverInterface
{
    protected Registry $registry;
    protected Data $dinitosHelper;

    public function __construct(
        Registry $registry,
        Data $dinitosHelper
    ) {
        $this->registry = $registry;
        $this->dinitosHelper = $dinitosHelper;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getData('quote');
        $totalDinitosQty = $this->dinitosHelper->getTotalDinitosQty($quote->getAllItems());

        $this->registry->unregister('quote_calculated_dinitos');
        $quote->setData('total_dinitos_qty', $totalDinitosQty);

        return $this;
    }
}
