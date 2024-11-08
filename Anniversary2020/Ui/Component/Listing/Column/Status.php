<?php

namespace Hiperdino\Anniversary2020\Ui\Component\Listing\Column;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const VALUE_PENDING = 0;
    const VALUE_PROCESS = 1;
    const VALUE_ERROR = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_PENDING, 'label' => __('Pendiente')],
            ['value' => self::VALUE_PROCESS, 'label' => __('Procesado')],
            ['value' => self::VALUE_ERROR, 'label' => __('Error')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::VALUE_PENDING => __('Pendiente'),
            self::VALUE_PROCESS => __('Procesado'),
            self::VALUE_ERROR => __('Error')
        ];
    }

}
