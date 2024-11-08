<?php

namespace Hiperdino\Dinitos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class DiscountTypeOptions extends Column implements OptionSourceInterface
{
    const REWARD_LABEL = 'Recompensa';
    const REWARD_VALUE = '0';

    protected array $options =
        [
            ['label' => self::REWARD_LABEL, 'value' => self::REWARD_VALUE]
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
