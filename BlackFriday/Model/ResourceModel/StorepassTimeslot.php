<?php

namespace Hiperdino\BlackFriday\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Singular\Delivery\Ui\Component\Listing\Column\Weekday\Options;

class StorepassTimeslot extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('hiperdino_blackfriday_storepass_timeslot', 'id');
    }

    public function save(AbstractModel $object)
    {
        $weekdays = $object->getWeekdays();
        sort($weekdays);
        $wd = [];
        foreach ($weekdays as $weekday) {
            if ($initial = array_search($weekday, Options::$weekdays)) {
                $wd[] = $initial;
            }
        }
        $object->setWeekdays(implode(',', $wd ?: []));

        return parent::save($object);
    }
}
