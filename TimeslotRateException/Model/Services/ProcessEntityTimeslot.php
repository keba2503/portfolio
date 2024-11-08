<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Singular\Delivery\Model\ResourceModel\Timeslot\CollectionFactory as TimeslotCollectionFactory;

class ProcessEntityTimeslot
{
    protected TimeslotCollectionFactory $timeSlotCollection;

    public function __construct(
        TimeslotCollectionFactory $timeSlotCollection,
    ) {
        $this->timeSlotCollection = $timeSlotCollection;
    }

    /**
     * @param array $entities
     * @param array $entityIds
     * @param $field
     * @param $key
     * @param $data
     * @return mixed
     */
    public function processEntitysTimeslot(array $entities, array $entityIds, $field, $key, $data)
    {
        foreach ($entities as $entity) {
            $timeSlotCollection = $this->timeSlotCollection->create();
            $timeSlotIds = $timeSlotCollection->addFieldToFilter(
                $field, ['in' => $entity->getData($key)]
            )->getItems();

            foreach ($timeSlotIds as $timeSlotId) {
                $entityIds[] = $timeSlotId->getData('id');
            }
        }

        return $this->mergeTimeSlots($data, $entityIds);
    }

    private function mergeTimeSlots($data, $timeSlotIds)
    {
        if (is_array($data['timeslots'])) {
            $data['timeslots'] = array_merge($timeSlotIds, $data['timeslots']);
        } else {
            $data['timeslots'] = $timeSlotIds;
        }

        return $data;
    }
}
