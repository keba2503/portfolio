<?php

namespace Hiperdino\BlackFriday\Model\ResourceModel\StorepassTimeslot;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_bf_storepasstimeslot_collection';
    protected $_eventObject = 'hiperdino_bf_storepasstimeslot_collection';

    protected function _construct()
    {
        $this->_init('Hiperdino\BlackFriday\Model\StorepassTimeslot', 'Hiperdino\BlackFriday\Model\ResourceModel\StorepassTimeslot');
    }
}
