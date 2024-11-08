<?php

namespace Hiperdino\BlackFriday\Model\ResourceModel\StorepassBooking;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_bf_storepassbooking_collection';
    protected $_eventObject = 'hiperdino_bf_storepassbooking_collection';

    protected function _construct()
    {
        $this->_init('Hiperdino\BlackFriday\Model\StorepassBooking', 'Hiperdino\BlackFriday\Model\ResourceModel\StorepassBooking');
    }
}
