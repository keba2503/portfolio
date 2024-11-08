<?php

namespace Hiperdino\BlackFriday\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Hiperdino\BlackFriday\Api\Data\StorepassTimeslotInterface;
use Singular\Delivery\Ui\Component\Listing\Column\Weekday\Options as WeekdaysOptions;

class StorepassTimeslot extends AbstractModel implements IdentityInterface, StorepassTimeslotInterface
{
    const CACHE_TAG = 'hiperdino_bf_storepasstimeslot';

    protected $_cacheTag = 'hiperdino_bf_storepasstimeslot';

    protected $_eventPrefix = 'hiperdino_bf_storepasstimeslot';

    protected function _construct()
    {
        $this->_init('Hiperdino\BlackFriday\Model\ResourceModel\StorepassTimeslot');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getWeekdaysIds()
    {
        $weekdaysIds = [];
        $weekdays = explode(",", $this->getData('weekdays') ?: "");
        foreach($weekdays as $weekday) {
            $weekdaysIds[] = WeekdaysOptions::$weekdays[$weekday];
        }
        return $weekdaysIds;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->getData('limit');
    }
}
