<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Magento\Store\Model\StoreManagerInterface;
use Singular\Tiendas\Model\Resource\Tiendas\CollectionFactory as StoreCollectionFactory;

class PickUp
{
    protected StoreManagerInterface $storeManager;
    protected StoreCollectionFactory $storeCollection;
    protected ProcessEntityTimeslot $entityTimeslot;

    public function __construct(
        ProcessEntityTimeslot $entityTimeslot,
        StoreManagerInterface $storeManager,
        StoreCollectionFactory $storeCollection
    ) {
        $this->entityTimeslot = $entityTimeslot;
        $this->storeManager = $storeManager;
        $this->storeCollection = $storeCollection;
    }

    public function getByPickUpStores($deliveryStoreViews)
    {
        $stores = $this->storeCollection->create();
        $stores->addFieldToFilter('store_code', ['in' => $deliveryStoreViews]);

        return $stores->getItems();
    }

    public function processStorePickUp($deliveryStoreViews, $data)
    {
        $physicalStores = $this->getByPickUpStores($deliveryStoreViews);
        $physicalIds = [];
        $field = 'parent_store';
        $key = 'id';

        return $this->entityTimeslot->processEntitysTimeslot($physicalStores, $physicalIds, $field, $key, $data);
    }
}
