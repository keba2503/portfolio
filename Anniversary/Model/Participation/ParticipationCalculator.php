<?php

namespace Hiperdino\Anniversary\Model\Participation;

use Exception;
use Hiperdino\Anniversary\Helper\Config;
use Hiperdino\Anniversary\Helper\ExtraInfo;
use Hiperdino\Anniversary\Helper\Product;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class ParticipationCalculator
{
    protected Config $config;
    protected ExtraInfo $extraInfo;
    protected Product $productManager;

    public function __construct(
        Config $config,
        ExtraInfo $extraInfo,
        Product $product
    ) {
        $this->config = $config;
        $this->extraInfo = $extraInfo;
        $this->productManager = $product;
    }

    /**
     * Recalcula los Rascas del Quote
     * @param Quote $quote
     * @throws Exception
     * return int
     */
    public function recalculateRascas($quote)
    {
        $items = $quote->getAllVisibleItems();

        foreach ($items as $item) {
            $itemProduct = $item->getProduct();
            $item->setIsSuperMode(true);
            $item->setData("is_anniversary_product", $this->productManager->isAnniversaryProduct($itemProduct->getProductTags()))->save();
        }

        $extraInfo = $quote->getData("extra_info");

        if ($this->config->promotionAvailable()) {
            $subtotal = $quote->getSubtotal();

            $rascasQty = $this->rascasQty($subtotal);
            $rascasExtraQty = $this->rascasExtraQty($subtotal, $items);
            $rascasTotalQty = $this->rascasQtyTotal($subtotal, $items);

            if ($rascasTotalQty) {
                $quote->setData("anniversary_qty", $rascasQty);
                $quote->setData("anniversary_extra_qty", $rascasExtraQty);
                $quote->setData("anniversary_total_qty", $rascasTotalQty);
                $quote->setData("extra_info", $this->extraInfo->getExtraInfoWithAnniversary($extraInfo, $rascasTotalQty));
            } else {
                $quote->setData("anniversary_total_qty", 0);
                $quote->setData("anniversary_qty", 0);
                $quote->setData("anniversary_extra_qty", 0);
                $quote->setData("extra_info", $this->extraInfo->getExtraInfoWithAnniversary($extraInfo, 0));
            }

        } else {
            $quote->setData("extra_info", $this->extraInfo->getExtraInfoWithAnniversary($extraInfo, 0, true));
            $quote->setData("anniversary_total_qty", null);
        }

        $quote->save();
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function recalculateRascasForOrder($order)
    {
        $extraInfo = $order->getData("extra_info");

        $anniversaryInfo = $this->extraInfo->getAnniversaryInfo($extraInfo);
        if (!$anniversaryInfo) return $order;

        $rascasQty = $this->rascasQtyForOrder($order->getSubtotal());
        $rascasExtraQty = $this->rascasQtyExtraForOrder($order->getSubtotal(), $order->getAllVisibleItems());
        $rascasTotalQty = $this->rascasQtyTotalForOrder($order->getSubtotal(), $order->getAllVisibleItems());

        if ($rascasTotalQty) {
            $order->setData("anniversary_qty", $rascasQty);
            $order->setData("anniversary_extra_qty", $rascasExtraQty);
            $order->setData("anniversary_total_qty", $rascasTotalQty);
            $order->setData("extra_info", $this->extraInfo->getExtraInfoWithAnniversary($extraInfo, $rascasTotalQty));
        } else {
            $order->setData("anniversary_total_qty", 0);
            $order->setData("anniversary_qty", 0);
            $order->setData("anniversary_extra_qty", 0);
            $order->setData("extra_info", $this->extraInfo->getExtraInfoWithAnniversary($extraInfo, 0));
        }

        return $order;
    }

    /**
     * Devuelve los rascas totales para el quote
     * @param $subtotal
     * @param $items
     * @return float|int
     */
    public function rascasQtyTotal($subtotal, $items)
    {
        return $this->rascasQtyForSubtotal($subtotal) + $this->rascasQtyForProduct($items, $subtotal);
    }

    /**
     * @param $subtotal
     * @param $items
     * @return float|int
     */
    public function rascasExtraQty($subtotal, $items)
    {
        return $this->rascasQtyForProduct($items, $subtotal) ;
    }

    /**
     * @param $subtotal
     * @return float|int
     */
    public function rascasQty($subtotal)
    {
        return $this->rascasQtyForSubtotal($subtotal);
    }

    /**
     * Devuelve los rascas totales para el order
     * @param $subtotal
     * @param $items
     * @return float|int
     */
    public function rascasQtyTotalForOrder($subtotal, $items)
    {
        return $this->rascasQtyForSubtotal($subtotal) + $this->rascasQtyForProductInOrder($items, $subtotal);
    }

    public function rascasQtyExtraForOrder($subtotal, $items)
    {
        return $this->rascasQtyForProductInOrder($items, $subtotal);
    }

    public function rascasQtyForOrder($subtotal)
    {
        return $this->rascasQtyForSubtotal($subtotal);
    }

    private function rascasQtyForSubtotal($subtotal)
    {
        $rascas = intval($subtotal / $this->config->getEurosByRascas());

        return $rascas * $this->config->getRascasQtyByEuros();
    }

    private function rascasQtyForProduct($items, $subtotal)
    {
        if ($subtotal < $this->config->getEurosByRascas()) {
            return 0;
        }

        $productAniversarioTotals = 0;
        foreach ($items as $item) {

            if ($item->getData("is_anniversary_product")) {
                $productAniversarioTotals += $item->getQty();
            }
        }

        if (intval($productAniversarioTotals) >= $this->config->getProductNumForExtra()) {
            return $this->config->getMaxRascasExtra();
        }

        return 0;
    }

    private function rascasQtyForProductInOrder($items, $subtotal)
    {
        if ($subtotal < $this->config->getEurosByRascas()) {
            return 0;
        }

        $productAniversarioTotals = 0;

        foreach ($items as $item) {
            if ($item->getData("is_anniversary_product")) {
                $productAniversarioTotals += $item->getQtyOrdered();
            }
        }

        if (intval($productAniversarioTotals) >= $this->config->getProductNumForExtra()) {
            return $this->config->getMaxRascasExtra();
        }

        return 0;
    }
}
