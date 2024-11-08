<?php

namespace Hiperdino\BlackFriday\Model;

use Hiperdino\BlackFriday\Api\Data\StorepassBookingInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class StorepassBooking extends AbstractModel implements IdentityInterface, StorepassBookingInterface
{
    const CACHE_TAG = 'hiperdino_bf_storepassbooking';

    protected $_cacheTag = 'hiperdino_bf_storepassbooking';

    protected $_eventPrefix = 'hiperdino_bf_storepassbooking';

    protected function _construct()
    {
        $this->_init('Hiperdino\BlackFriday\Model\ResourceModel\StorepassBooking');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getQrImagePath()
    {
        $qr = $this->getData('qr');
        return "qr/bf_storepass/{$qr}";
    }
}
