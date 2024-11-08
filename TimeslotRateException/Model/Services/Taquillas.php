<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Hiperdino\Taquillas\Model\ResourceModel\Strongpoint\CollectionFactory as StrongpointCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Taquillas
{
    protected StoreManagerInterface $storeManager;
    protected StrongpointCollectionFactory $strongPointCollection;
    protected ProcessEntityTimeslot $entityTimeslot;

    public function __construct(
        StrongpointCollectionFactory $strongPointCollection,
        ProcessEntityTimeslot $entityTimeslot,
        StoreManagerInterface $storeManager
    ) {
        $this->strongPointCollection = $strongPointCollection;
        $this->entityTimeslot = $entityTimeslot;
        $this->storeManager = $storeManager;
    }

    public function getByLockers($deliveryStoreViews)
    {
        $lockers = $this->strongPointCollection->create();
        $lockers->addFieldToFilter('shop_code', ['in' => $deliveryStoreViews]);

        return $lockers->getItems();
    }

    public function processTaquilla($deliveryStoreViews, $data)
    {
        $lockers = $this->getByLockers($deliveryStoreViews);
        $lockersIds = [];
        $field = 'taquilla_point';
        $key = 'strongpoint_id';

        return $this->entityTimeslot->processEntitysTimeslot($lockers, $lockersIds, $field, $key, $data);
    }
}
