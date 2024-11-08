<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Magento\Customer\Model\Session;
use Singular\Delivery\Api\BookingRepositoryInterface;

class CustomerSelectedTimeslot
{
    protected $selectedTimeslot = false;
    protected $selectedDate = false;

    public function __construct(
        protected readonly Session $customerSession,
        protected readonly BookingRepositoryInterface $bookingRepository
    ) {
    }

    public function getTimeslot()
    {
        if (!$this->selectedTimeslot) {
            $selectedTimeslot = $this->customerSession->getData('selected_timeslot');
            if ($selectedTimeslot) {
                $this->selectedTimeslot = $selectedTimeslot;
                $this->selectedDate = $this->customerSession->getData('selected_timeslot_date');
            } else {
                $customerId = $this->customerSession->getCustomerId();
                $booking = $this->bookingRepository->getByCustomerId($customerId);
                if ($booking) {
                    $this->selectedTimeslot = $booking->getTimeslotId();
                    $this->selectedDate = $booking->getBookedFor();
                }
            }
        }

        return [$this->selectedTimeslot, $this->selectedDate];
    }

    public function setTimeslot($timeslotId, $date)
    {
        $this->customerSession->setData('selected_timeslot', $timeslotId);
        $this->customerSession->setData('selected_timeslot_date', $date);
        $this->selectedTimeslot = false;
    }
}
