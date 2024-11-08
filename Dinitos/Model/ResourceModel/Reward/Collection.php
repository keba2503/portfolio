<?php

namespace Hiperdino\Dinitos\Model\ResourceModel\Reward;

use Hiperdino\Dinitos\Ui\Component\Listing\Column\RewardsTypeOptions;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_dinitos_rewards_collection';
    protected $_eventObject = 'hiperdino_dinitos_rewards_collection';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Hiperdino\Dinitos\Model\Data\Reward', 'Hiperdino\Dinitos\Model\ResourceModel\Reward');
    }

    public function addStoreFilter($websiteId): static
    {
        $this->addFieldToFilter("stores", ["finset" => $websiteId]);

        return $this;
    }

    public function addDiscountTypeFilter()
    {
        $this->addFieldToFilter('type', RewardsTypeOptions::DISCOUNT_VALUE);

        return $this;
    }
}