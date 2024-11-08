<?php

namespace Hiperdino\TimeslotRateException\Plugin;

use Hiperdino\Dinitos\Model\Services\Shipping\UpdateFreeShippingIndicator;
use Hiperdino\TimeslotRateException\Model\Services\CalculateTimeslotRateException;
use Hiperdino\TimeslotRateException\Model\Services\CustomerSelectedTimeslot;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

class CarrierCollectTotalAround
{
    public function __construct(
        protected readonly CustomerSelectedTimeslot $customerSelectedTimeslot,
        protected readonly CalculateTimeslotRateException $calculateTimeslotRateException,
        protected UpdateFreeShippingIndicator $updateFreeShippingIndicator
    ) {
    }

    public function afterCollectRates(AbstractCarrier $subject, $result)
    {
        list($timeslotId, $timeslotDate) = $this->customerSelectedTimeslot->getTimeslot();
        if ($timeslotId && $timeslotDate) {
            $timeslotRatePrice = $this->calculateTimeslotRateException->execute($timeslotId, $timeslotDate);
            $shippingIndicator = $this->updateFreeShippingIndicator->getFreeShippingIndicator() == 'timeslot_rate_exception';

            if (($timeslotRatePrice === null || $timeslotRatePrice > UpdateFreeShippingIndicator::SHIPPING_FREE) && $shippingIndicator) {
                $this->updateFreeShippingIndicator->execute();
            }

            if (isset($timeslotRatePrice)) {
                $result->getAllRates()[0]['price'] = $timeslotRatePrice;
                if ($timeslotRatePrice == UpdateFreeShippingIndicator::SHIPPING_FREE) {
                    $this->updateFreeShippingIndicator->execute('timeslot_rate_exception');
                }
            }
        }

        return $result;
    }
}