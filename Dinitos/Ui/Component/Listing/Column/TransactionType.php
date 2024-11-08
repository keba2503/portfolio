<?php

namespace Hiperdino\Dinitos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TransactionType extends Column implements OptionSourceInterface
{
    const LABEL_ACCUMULATED = "Acumulados";
    const LABEL_REDEEMED = "Canjeados";
    const LABEL_EXPIRED = "Caducados";
    const LABEL_REFUND = "Reembolso";
    const LABEL_DEDUCTION = "Deducción";
    const VALUE_ACCUMULATED = 0;
    const VALUE_REDEEMED = 1;
    const VALUE_EXPIRED = 2;
    const VALUE_REFUND = 3;
    const VALUE_DEDUCTION = 4;

    protected array $options = [
        ['label' => self::LABEL_ACCUMULATED, 'value' => self::VALUE_ACCUMULATED],
        ['label' => self::LABEL_REDEEMED, 'value' => self::VALUE_REDEEMED],
        ['label' => self::LABEL_EXPIRED, 'value' => self::VALUE_EXPIRED],
        ['label' => self::LABEL_REFUND, 'value' => self::VALUE_REFUND],
        ['label' => self::LABEL_DEDUCTION, 'value' => self::VALUE_DEDUCTION]
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
