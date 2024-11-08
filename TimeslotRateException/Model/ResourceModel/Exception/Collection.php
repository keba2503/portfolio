<?php

namespace Hiperdino\TimeslotRateException\Model\ResourceModel\Exception;

use Hiperdino\TimeslotRateException\Api\Data\ExceptionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'hiperdino_delivery_rate_exception';
    protected $_eventObject = 'hiperdino_delivery_rate_exception';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Hiperdino\TimeslotRateException\Model\Data\Exception', 'Hiperdino\TimeslotRateException\Model\ResourceModel\Exception');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(
                ['timeslots' => $this->getTable('hiperdino_delivery_rate_exception_timeslot')],
                'main_table.id = timeslots.exception_id',
                ['timeslots' => 'timeslots.timeslot_id'])
            ->group('main_table.id');

        $this->addFilterToMap('timeslots', 'timeslots.timeslot_id');
        $this->addFilterToMap('id', 'main_table.id');

        return $this;
    }

    public function getActiveExceptions($date)
    {
        $this->addFieldToFilter(ExceptionInterface::IS_ACTIVE, true);
        $this->addFieldToFilter([ExceptionInterface::START_DATE, ExceptionInterface::START_DATE], [['lteq' => $date], ['null' => true]]);
        $this->addFieldToFilter([ExceptionInterface::END_DATE, ExceptionInterface::END_DATE], [['gteq' => $date], ['null' => true]]);

        return $this;
    }
}
