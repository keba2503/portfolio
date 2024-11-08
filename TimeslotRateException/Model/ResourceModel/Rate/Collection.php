<?php

namespace Hiperdino\TimeslotRateException\Model\ResourceModel\Rate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_delivery_rate';
    protected $_eventObject = 'hiperdino_delivery_rate';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiperdino\TimeslotRateException\Model\Data\Rate', 'Hiperdino\TimeslotRateException\Model\ResourceModel\Rate');
    }
}
