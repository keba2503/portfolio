<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Magento\Store\Model\StoreManagerInterface;
use Singular\Delivery\Model\ResourceModel\Zone\CollectionFactory as ZoneCollectionFactory;

class FlatRate
{
    protected StoreManagerInterface $storeManager;
    protected ZoneCollectionFactory $zoneCollection;
    protected ProcessEntityTimeslot $entityTimeslot;

    public function __construct(
        ZoneCollectionFactory $zoneCollection,
        ProcessEntityTimeslot $entityTimeslot,
        StoreManagerInterface $storeManager
    ) {
        $this->zoneCollection = $zoneCollection;
        $this->entityTimeslot = $entityTimeslot;
        $this->storeManager = $storeManager;
    }

    public function getByDeliveryZone($deliveryStoreViews)
    {
        $zones = [];
        foreach ($deliveryStoreViews as $value) {
            $allStores = $this->storeManager->getStores(true, false);
            foreach ($allStores as $store) {
                if ($store->getCode() === $value) {
                    $storeId = $store->getId();

                    $zone = $this->zoneCollection->create();
                    $zone->addFieldToFilter('store_id', ['in' => $storeId]);
                    $zones += $zone->getItems();
                }
            }

        }

        return $zones;
    }

    public function processFlatRate($deliveryStoreViews, $data)
    {
        $deliveryByZones = $this->getByDeliveryZone($deliveryStoreViews);
        $deliveryIds = [];
        $field = 'parent_zone';
        $key = 'id';

        return $this->entityTimeslot->processEntitysTimeslot($deliveryByZones, $deliveryIds, $field, $key, $data);
    }
}
