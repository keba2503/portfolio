<?php

namespace Hiperdino\Dinitos\Plugin;

use Hiperdino\Dinitos\Helper\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;

class AfterTaquillasCollectRates
{
    protected Config $dinitosConfig;
    protected Registry $registry;

    public function __construct(
        Config $dinitosConfig,
        Registry $registry
    ) {
        $this->dinitosConfig = $dinitosConfig;
        $this->registry = $registry;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function afterCollectRates($carrier, $result)
    {
        $enabledTaquillasDinitos = $this->dinitosConfig->getDinitosActive('taquillas');

        if ($enabledTaquillasDinitos && $result) {
            /** @var Quote $quote */
            $quote = $this->registry->registry('quote_shipping_rates');
            if (!$quote) {
                return $result;
            }
            $totalDinitosQty = $quote->getData('total_dinitos_qty');
            if ($totalDinitosQty) {
                $taquillasDinitos = $this->dinitosConfig->getDinitosQty('taquillas');
                if ($totalDinitosQty >= $taquillasDinitos) {
                    foreach ($result->getAllRates() as $method) {
                        $method->setPrice(0.0);
                        $method->setCost(0.0);
                        $method->setData("dinitos_free", 1);
                    }
                }
            }
        }

        return $result;
    }
}