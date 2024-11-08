<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Singular\Delivery\Model\Config\Source\DeliveryTypeOptions;

class DeliveryTypeService
{
    protected DataPersistorInterface $dataPersistor;
    protected StoreManagerInterface $storeManager;
    protected FlatRate $flatRate;
    protected PickUp $pickUp;
    protected PickUpPoint $pickUpPoint;
    protected Taquillas $taquillas;

    public function __construct(
        DataPersistorInterface $dataPersistor,
        FlatRate $flatRate,
        PickUp $pickUp,
        PickUpPoint $pickUpPoint,
        Taquillas $taquillas
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->flatRate = $flatRate;
        $this->pickUp = $pickUp;
        $this->pickUpPoint = $pickUpPoint;
        $this->taquillas = $taquillas;
    }

    /**
     * @param $data
     * @return array
     */
    public function extractDeliveryInfo($data): array
    {
        $deliveryType = (!empty($data['delivery_type'])) ? $data['delivery_type'] : '';
        $deliveryStoreViews = (!empty($data['delivery_store_views'])) ? $data['delivery_store_views'] : '';

        return array($data, $deliveryType, $deliveryStoreViews);
    }

    public function prepareTimeslotsDataFromDeliveryType($deliveryType, $deliveryStoreViews, $data)
    {
        if (!empty($deliveryType) && !empty($deliveryStoreViews)) {
            foreach ($deliveryType as $value) {
                $data = $this->processDeliveryType($value, $deliveryStoreViews, $data);
            }
        }

        return $data;
    }

    private function processDeliveryType($deliveryType, $deliveryStoreViews, $data)
    {
        return match ($deliveryType) {
            DeliveryTypeOptions::FLAT_RATE_CODE => $this->flatRate->processFlatRate($deliveryStoreViews, $data),
            DeliveryTypeOptions::STORE_PICK_UP_CODE => $this->pickUp->processStorePickUp($deliveryStoreViews, $data),
            DeliveryTypeOptions::PICK_UP_POINT_CODE => $this->pickUpPoint->processPickUpPoint($deliveryStoreViews, $data),
            DeliveryTypeOptions::TAQUILLA_CODE => $this->taquillas->processTaquilla($deliveryStoreViews, $data),
            default => $data
        };
    }
}
