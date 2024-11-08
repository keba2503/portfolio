<?php

namespace Hiperdino\Dinitos\Plugin;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Model\Services\Shipping\UpdateFreeShippingIndicator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;

class AfterPickupCollectRates
{
    protected Config $dinitosConfig;
    protected Registry $registry;
    protected UpdateFreeShippingIndicator $updateFreeShippingIndicator;

    public function __construct(
        Config $dinitosConfig,
        UpdateFreeShippingIndicator $updateFreeShippingIndicator,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->dinitosConfig = $dinitosConfig;
        $this->updateFreeShippingIndicator = $updateFreeShippingIndicator;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function afterCollectRates($carrier, $result)
    {
        $enabledPickupDinitos = $this->dinitosConfig->getDinitosActive('pickup');

        if ($enabledPickupDinitos && $result) {
            /** @var Quote $quote */
            $quote = $this->registry->registry('quote_shipping_rates');
            if (!$quote) {
                return $result;
            }
            $totalDinitosQty = $quote->getData('total_dinitos_qty');
            if ($totalDinitosQty) {
                $pickupDinitos = $this->dinitosConfig->getDinitosQty('pickup');
                if ($totalDinitosQty >= $pickupDinitos) {
                    foreach ($result->getAllRates() as $method) {
                        $method->setPrice(0.0);
                        $method->setCost(0.0);
                        $method->setData("dinitos_free", 1);
                        $this->updateFreeShippingIndicator->execute('Dinitos');
                    }
                }
            }
        }

        return $result;
    }
}
