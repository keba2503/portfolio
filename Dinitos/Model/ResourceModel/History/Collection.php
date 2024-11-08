<?php

namespace Hiperdino\Dinitos\Model\ResourceModel\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_dinitos_history';
    protected $_eventObject = 'hiperdino_dinitos_history';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiperdino\Dinitos\Model\Data\History', 'Hiperdino\Dinitos\Model\ResourceModel\History');
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);

        return $this;
    }

    public function addCustomerAndIncrementIdFilter($customerId, $incrementId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        $this->addFieldToFilter('increment_id', $incrementId);

        return $this;
    }
}
