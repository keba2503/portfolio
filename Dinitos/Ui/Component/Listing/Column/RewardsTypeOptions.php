<?php

namespace Hiperdino\Dinitos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class RewardsTypeOptions extends Column implements OptionSourceInterface
{
    const PRODUCT_LABEL = 'Producto';
    const SHIPPING_LABEL = 'Envío';
    const DISCOUNT_LABEL = 'Descuento';
    const PRODUCT_VALUE = 0;
    const SHIPPING_VALUE = 1;
    const DISCOUNT_VALUE = 2;

    protected array $options = [
        ['label' => self::PRODUCT_LABEL, 'value' => self::PRODUCT_VALUE],
        ['label' => self::SHIPPING_LABEL, 'value' => self::SHIPPING_VALUE],
        ['label' => self::DISCOUNT_LABEL, 'value' => self::DISCOUNT_VALUE]
    ];

    public function toOptionArray(): array
    {
        return $this->options;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = $this->prepareItem($item);
                }
            }
        }

        return $dataSource;
    }

    protected function prepareItem(array $item): string
    {
        $content = "";
        $optionByItemValue = array_filter($this->options, function ($option) use ($item) {
            return $option['value'] == $item[$this->getData('name')];
        });
        $content .= reset($optionByItemValue)['label'];

        return $content;
    }
}
