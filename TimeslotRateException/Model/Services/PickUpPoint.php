<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Hiperdino\PickupPoints\Model\Resource\PickupPoints\CollectionFactory as PickUpPointsCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class PickUpPoint
{
    protected PickUpPointsCollectionFactory $pickUpPointsCollection;
    protected ProcessEntityTimeslot $entityTimeslot;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        PickUpPointsCollectionFactory $pickUpPointsCollection,
        ProcessEntityTimeslot $entityTimeslot,
        StoreManagerInterface $storeManager
    ) {
        $this->pickUpPointsCollection = $pickUpPointsCollection;
        $this->entityTimeslot = $entityTimeslot;
        $this->storeManager = $storeManager;
    }

    public function getByPickUpPoints($deliveryStoreViews)
    {
        $pickUpPoints = $this->pickUpPointsCollection->create();
        $pickUpPoints->addFieldToFilter('store_code', ['in' => $deliveryStoreViews]);

        return $pickUpPoints->getItems();
    }

    public function processPickUpPoint($deliveryStoreViews, $data)
    {
        $pickUpPoints = $this->getByPickUpPoints($deliveryStoreViews);
        $pickUpPointsIds = [];
        $field = 'parent_point';
        $key = 'id';

        return $this->entityTimeslot->processEntitysTimeslot($pickUpPoints, $pickUpPointsIds, $field, $key, $data);
    }
}
