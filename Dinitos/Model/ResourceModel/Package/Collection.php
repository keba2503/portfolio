<?php

namespace Hiperdino\Dinitos\Model\ResourceModel\Package;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_dinitos_packages_collection';
    protected $_eventObject = 'hiperdino_dinitos_packages_collection';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Hiperdino\Dinitos\Model\Data\Package', 'Hiperdino\Dinitos\Model\ResourceModel\Package');
    }

    public function addCustomerRedeemableFilter($customerId): static
    {
        $this->addCustomerFilter($customerId)->addFieldToFilter('expired', 0)->addFieldToFilter('redeemed', 0)->addOrder('expiration_date', self::SORT_ORDER_ASC);

        return $this;
    }

    public function addExpiredFilter(): static
    {
        $this->addFieldToFilter('expired', 0)->addFieldToFilter('redeemed', 0)->addOrder('expiration_date', self::SORT_ORDER_ASC);

        return $this;
    }

    public function addCustomerFilter($customerId): static
    {
        $this->addFieldToFilter('customer_id', $customerId);

        return $this;
    }
}