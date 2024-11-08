<?php

namespace Hiperdino\BlackFriday\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StorepassBooking extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('hiperdino_blackfriday_storepass_booking', 'id');
    }
}
