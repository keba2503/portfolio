<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Hiperdino\TimeslotRateException\Api\Data\RateInterface;
use Hiperdino\TimeslotRateException\Model\ResourceModel\Exception\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\Helper\Data;

class GetActiveRateExceptionTimeslots
{
    public function __construct(
        protected readonly CollectionFactory $timeslotRateExceptionCollection,
        protected readonly ResourceConnection $resourceConnection,
        protected readonly Data $pricingHelper
    ) {
    }

    public function execute($date)
    {
        $rateCollection = $this->timeslotRateExceptionCollection->create();
        $rateExceptions = $rateCollection->getActiveExceptions($date);
        $rateExceptionTimeslots = [];
        foreach ($rateExceptions as $rateException) {
            $currentRateExceptionTimeslots = $this->getTimeslots($rateException->getId());
            $ratePrice = $this->getTimeslotPrice($rateException);
            foreach ($currentRateExceptionTimeslots as $currentRateExceptionTimeslot) {
                $rateExceptionTimeslots[$currentRateExceptionTimeslot] = $ratePrice;
            }
        }

        return $rateExceptionTimeslots;
    }

    protected function getTimeslots($id)
    {
        $connection = $this->resourceConnection->getConnection();
        $timeslotsSelect = $connection->fetchAll("SELECT timeslot_id FROM hiperdino_delivery_rate_exception_timeslot WHERE exception_id = {$id}");
        $timeslots = [];
        foreach ($timeslotsSelect as $timeslot) {
            $timeslots[] = $timeslot['timeslot_id'];
        }

        return $timeslots ?: [];
    }

    /**
     * @param mixed $rateException
     * @return float|bool|string
     */
    protected function getTimeslotPrice(mixed $rateException): float|bool|string
    {
        /** @var RateInterface $rate */
        $rate = $rateException->getRateInfo();
        if ($rate && $rate->getId()) {
            return $this->pricingHelper->currency($rate->getAmount(), true, false);
        }

        return false;
    }
}
