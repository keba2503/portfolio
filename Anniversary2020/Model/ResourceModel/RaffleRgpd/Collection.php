<?php

namespace Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_rafflergpd_collection';
    protected $_eventObject = 'hiperdino_rafflergpd_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiperdino\Anniversary2020\Model\RaffleRgpd', 'Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd');
    }
}
