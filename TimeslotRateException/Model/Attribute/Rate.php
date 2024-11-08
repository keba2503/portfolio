<?php

namespace Hiperdino\TimeslotRateException\Model\Attribute;

use Hiperdino\TimeslotRateException\Model\Data\RateFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Rate extends AbstractSource
{
    protected RateFactory $rateFactory;

    public function __construct(
        RateFactory $rateFactory
    ) {
        $this->rateFactory = $rateFactory;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [['value' => "", 'label' => " "]];
            $rates = $this->rateFactory->create()->getCollection();
            foreach ($rates->getItems() as $rate) {
                $this->_options[] = [
                    'value' => $rate->getId(),
                    'label' => $rate->getName()
                ];
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getAllOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
}
