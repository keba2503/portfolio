<?php

namespace Hiperdino\Anniversary\Observer;

use Hiperdino\Anniversary\Helper\ExtraInfo;
use Hiperdino\Anniversary\Helper\Config;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddAnniversaryVariableToInvoiceEmail implements ObserverInterface
{
    protected ExtraInfo $extraInfo;
    protected Config $configHelper;

    public function __construct(
        ExtraInfo $extraInfo,
        Config $configHelper
    ) {
        $this->extraInfo = $extraInfo;
        $this->configHelper = $configHelper;
    }

    public function getAnniversaryInfo($order)
    {
        return $this->extraInfo->getAnniversaryInfo($order->getData("extra_info"));
    }

    public function getAnniversaryQty($order)
    {
        return $order->getData("anniversary_qty");
    }

    public function getAnniversaryExtraQty($order)
    {
        return $order->getData("anniversary_extra_qty");
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $transport = $observer->getData('transportObject');
        $order = $transport->getOrder();

        if ($order) {
            $anniversaryQty = $this->getAnniversaryQty($order);
            $anniversaryExtraQty = $this->getAnniversaryExtraQty($order);

            $timesLabel = $anniversaryQty > 1 ?
                $this->configHelper->getRascaTimeLabelPlural() :
                $this->configHelper->getRascaTimeLabelSingle();

            $participationLabel = $anniversaryExtraQty > 1 ?
                $this->configHelper->getRascaExtraLabelPlural() :
                $this->configHelper->getRascaExtraLabelSingle();

            if ($this->getAnniversaryInfo($order) && $anniversaryQty) {
                $transport->setData('anniversary_qty', $anniversaryQty);
                $transport->setData('anniversary_extra_qty', $anniversaryExtraQty);
                $transport->setData('times_label', $timesLabel);
                $transport->setData('participation_label', $participationLabel);
                $transport->setData('has_anniversary', true);
            } else {
                $transport->setData('has_anniversary', false);
            }

            if ($anniversaryExtraQty > 0) {
                $transport->setData('has_extras', true);
            } else {
                $transport->setData('has_extras', false);
            }
        }
    }
}