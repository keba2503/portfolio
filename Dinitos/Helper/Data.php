<?php

namespace Hiperdino\Dinitos\Helper;

use Magento\Quote\Model\Quote\Item;

class Data
{
    public function getDinitosLabel($has_dinitos, $dinitos_qty)
    {
        $label = false;
        $dinitos_qty = (int)trim($dinitos_qty ?: "");

        if ($has_dinitos && $dinitos_qty > 0) {
            if ($dinitos_qty === 1) $label = "dinito";
            if ($dinitos_qty > 1) $label = "dinitos";

            $label = $dinitos_qty . ' ' . $label;

            if ($dinitos_qty === 100) {
                $label = "1 dinito x 100g";
            }
        }

        return $label;
    }

    public function getTotalDinitosQty($allItems): int
    {
        $totalDinitosQty = 0;

        foreach ($allItems as $item) {
            /** @var Item $item */
            $itemProduct = $item->getProduct();
            $hasDinitos = (int)$itemProduct->getData('has_dinitos');

            if (!$hasDinitos) continue;

            $dinitosQty = (int)$itemProduct->getData('dinitos_qty');
            if ($dinitosQty <= 0) continue;

            // Si es 100 se pide por gramos
            $dinitosByGr = $dinitosQty === 100 ? 1 : 0;

            $measureUnit = $itemProduct->getAttributeText("measure_unit");
            $showPriceUnitBy = $itemProduct->getAttributeText('show_price_unit_by');
            $weight = $item->getWeight();

            try {
                // Productos que son al Kilo pero se piden por unidades
                if (str_contains($item->getSku(), '-UN') || $showPriceUnitBy === "KG" && $measureUnit === "UN") {
                    if (!$weight) continue;
                    $qty = $item->getQty() * $weight;
                } else {
                    $qty = $item->getQty();
                }

                if ($dinitosByGr) {
                    // Puede conseguir un máx de 7 dinitos
                    $totalDinitosQty += intval(floor($qty * 10) > 7 ? 7 : floor($qty * 10));
                } else {
                    $totalDinitosQty += intval($qty * $dinitosQty);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $totalDinitosQty;
    }
}
