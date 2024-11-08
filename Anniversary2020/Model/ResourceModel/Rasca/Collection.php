<?php

namespace Hiperdino\Anniversary2020\Model\ResourceModel\Rasca;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_anniversary2020_rasca_collection';
    protected $_eventObject = 'hiperdino_anniversary2020_rasca_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiperdino\Anniversary2020\Model\Rasca', 'Hiperdino\Anniversary2020\Model\ResourceModel\Rasca');
    }
}
