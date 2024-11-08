<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Hiperdino\BlackFriday\Model\StorepassTimeslotRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Hiperdino\BlackFriday\Model\StorepassBookingRepository;
use Singular\QRManager\Helper\QR;
use Magento\Framework\Filesystem\DirectoryList;
use Singular\Tiendas\Model\TiendasRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Singular\QRManager\Helper\Crypt as QrCrypt;

class StorePass extends AbstractHelper
{

    const SESSION_STOREPASS_IS_MODIFYING = 'bf_storepass_is_modifying';

    protected $_timeslotsRepository;
    protected $_searchCriteriaBuilder;
    protected $_bookingRepository;
    protected $_qrManager;
    protected $_directoryList;
    protected $_storePassEmailHelper;
    protected $_customerRepository;
    protected $_tiendasRepository;
    protected $_qrCrypt;

    public function __construct(
        TiendasRepository $tiendasRepository,
        CustomerRepositoryInterface $customerRepository,
        StorePassEmail $storePassEmailHelper,
        DirectoryList $directoryList,
        QR $qrManager,
        StorepassTimeslotRepository $storepassTimeslotRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StorepassBookingRepository $bookingRepository,
        QrCrypt $qrCrypt,
        Context $context
    ) {
        $this->_timeslotsRepository = $storepassTimeslotRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_bookingRepository = $bookingRepository;
        $this->_qrManager = $qrManager;
        $this->_directoryList = $directoryList;
        $this->_storePassEmailHelper = $storePassEmailHelper;
        $this->_tiendasRepository = $tiendasRepository;
        $this->_customerRepository = $customerRepository;
        $this->_qrCrypt = $qrCrypt;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/active');
    }

    /**
     * @param mixed $shopId
     * @return array
     */
    public function getTimeslotsCalendarByShopId($shopId)
    {
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('parent_store', $shopId)
            ->addFilter('is_active', 1)
            ->create();
        /** @var \Magento\Framework\Api\SearchResults $timeslots */
        $timeslots = $this->_timeslotsRepository->getList($searchCriteria);

        $timezone = new \DateTimeZone('Atlantic/Canary');

        $maxDays = (int) $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/max_days_for_booking');
        if(!$maxDays || !is_numeric($maxDays)) {
            $maxDays = 7;
        }

        $calendar = [];

        $allowSameDay = (bool) $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/allow_same_day');
        $firstDateToShow = $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/timeslot_date_start');
        $lastDateToShow = $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/timeslot_date_end');

        for ($days = 0; $days <= $maxDays; $days++) {
            if (!$allowSameDay && $days === 0) continue;
            try {
                $date = new \DateTime("now", $timezone);
            } catch(\Exception $e) {
                continue;
            }
            $date->modify("+{$days} days");
            $dayOfWeek = $date->format("w");
            if ($dayOfWeek == "0") {
                $dayOfWeek = "7"; // sunday fix
            }
            if($firstDateToShow && $this->_isDateGreaterThanDate($firstDateToShow, $date->format('Y/m/d'))) {
                continue;
            }
            if($lastDateToShow && $this->_isDateGreaterThanDate($date->format('Y/m/d'), $lastDateToShow)) {
                continue;
            }
            $totalDate = $date->format("d/m/Y");
            $totalDateTime = $date->format("H:m");
            $thisDateTimeslots = $this->_getTimeslotsByWeekday($timeslots, $dayOfWeek);

            foreach($thisDateTimeslots as $timeslot) {
                if ($allowSameDay && $days === 0) {
                    if ($timeslot->getStartTime() < $totalDateTime) continue;
                }
                $calendar[] = [
                    'id' => $timeslot->getId(),
                    'date' => $totalDate,
                    'start_time' => $timeslot->getStartTime(),
                    'end_time' => $timeslot->getEndTime(),
                    'available' => $this->isTimeslotAvailableForDate($timeslot, $date->format('Y-m-d'))
                ];
            }
        }

        return $calendar;
    }

    /**
     * @param \Magento\Framework\Api\SearchResults $currentTimeslots
     * @param mixed $weekdayId
     * @return array
     */
    protected function _getTimeslotsByWeekday($currentTimeslots, $weekdayId)
    {
        $timeslots = [];
        /** @var \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot */
        foreach ($currentTimeslots->getItems() as $timeslot) {
            if (in_array($weekdayId, $timeslot->getWeekdaysIds())) {
                $timeslots[] = $timeslot;
            }
        }
        return $timeslots;
    }

    /**
     * Date string format to compare: Y/m/d
     *
     * @param string $dateA
     * @param string $dateB
     * @return bool
     */
    protected function _isDateGreaterThanDate($dateA, $dateB)
    {
        $dateA = str_replace("/", "-", $dateA ?: "");
        $dateB = str_replace("/", "-", $dateB ?: "");
        return $dateA > $dateB;
    }

    /**
     * @param \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot
     * @param string $dateFor
     * @return bool
     */
    public function isTimeslotAvailableForDate($timeslot, $dateFor)
    {
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('timeslot_id', $timeslot->getId())
            ->addFilter('booked_for', $dateFor)
            ->create();
        /** @var \Magento\Framework\Api\SearchResults $bookings */
        $bookings = $this->_bookingRepository->getList($searchCriteria);
        return $timeslot->getLimit() > $bookings->getTotalCount();
    }

    /**
     * @param $customerId
     * @return bool
     */
    public function customerAlreadyHasBooking($customerId)
    {
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('customer_id', $customerId)
            ->create();
        /** @var \Magento\Framework\Api\SearchResults $bookings */
        $bookings = $this->_bookingRepository->getList($searchCriteria);
        return $bookings->getTotalCount() > 0;
    }

    /**
     * @param mixed $customerId
     * @param \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot
     * @param string $dateFor
     * @param bool $isModifying
     * @return \Hiperdino\BlackFriday\Model\StorepassBooking
     * @throws \Exception
     */
    public function createBooking($customerId, $timeslot, $dateFor, $isModifying = false)
    {
        if(!$isModifying && $this->customerAlreadyHasBooking($customerId)) {
            throw new \Exception(__('No puedes tener más de una reserva a la vez.'));
        }
        if(! $this->isTimeslotAvailableForDate($timeslot, $dateFor)) {
            throw new \Exception(__('No hay más cupo disponible para el día y la franja horaria seleccionada.'));
        }
        if($isModifying) {
            // Si está en proceso de modificación, cancelamos primero su reserva anterior.
            $bookingInfo = $this->getCurrentBookingInfo($customerId);
            /** @var \Hiperdino\BlackFriday\Model\StorepassBooking $booking */
            $booking = $bookingInfo['booking'];
            $this->cancelBookingForCustomer($booking->getId(), $customerId);
        }
        $booking = $this->_bookingRepository->create([
            'timeslot_id' => $timeslot->getId(),
            'customer_id' => $customerId,
            'booked_for' => $dateFor,
            'people' => 1,
            'turn_number' => 0,
            'qr' => ''
        ]);
        $this->_bookingRepository->save($booking);
        if($booking->getId()) {
            $qrCode = $this->_generateQrForBooking($booking, $timeslot);
            $booking->setData('qr', $qrCode);
            $this->_bookingRepository->save($booking);
        }
        $this->_storePassEmailHelper->sendBookingEmail($booking);
        return $booking;
    }

    /**
     * @param mixed $customerId
     * @return array|bool
     */
    public function getCurrentBookingInfo($customerId)
    {
        try {
            $booking = $this->_bookingRepository->getByCustomerId($customerId);
            $timeslot = $this->_timeslotsRepository->getById($booking->getData('timeslot_id'));
            return [
                'booking' => $booking,
                'timeslot' => $timeslot
            ];
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $bookingId
     * @param mixed $customerId
     * @throws \Exception
     */
    public function cancelBookingForCustomer($bookingId, $customerId)
    {
        /** @var \Hiperdino\BlackFriday\Model\StorepassBooking $booking */
        $booking = $this->_bookingRepository->getById($bookingId);
        if($booking->getData('customer_id') != $customerId) {
            throw new \Exception(__('La reserva no pertenece al cliente especificado.'));
        }
        $qrFilename = $booking->getData('qr');
        $this->_bookingRepository->delete($booking);
        if($qrFilename) {
            $baseMediaDir = $this->_directoryList->getPath('media');
            $qrFullPath = "{$baseMediaDir}/qr/bf_storepass/{$qrFilename}";
            @unlink($qrFullPath);
        }
    }


    /**
     * @param \Hiperdino\BlackFriday\Model\StorepassBooking $booking
     * @param \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot
     * @return string
     */
    protected function _generateQrForBooking($booking, $timeslot)
    {
        $startTime = $timeslot->getData('start_time');
        $startTimestamp = strtotime("{$booking->getData('booked_for')} {$startTime}");
        $customerId = $booking->getData('customer_id');
        try {
            $customer = $this->_customerRepository->getById($customerId);
            /** @var \Singular\Tiendas\Model\Tiendas $tienda */
            $tienda = $this->_tiendasRepository->getById($timeslot->getData('parent_store'));
        } catch(\Exception $e) {
            return '';
        }
        if(! $tienda->getData('offline_store_code')) {
            return '';
        }
        $shopCode = $tienda->getData('offline_store_code');
        if($customer->getCustomAttribute('sherpa_code') && $customer->getCustomAttribute('sherpa_code')->getValue()) {
            $customerCusId = $customer->getCustomAttribute('sherpa_code')->getValue();
        } else {
            $customerCusId = (string) $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/default_cus_id');
        }
        $promotionId = (string) $this->scopeConfig->getValue('hiperdino_blackfriday/storepass/promotion_id');
        $message = sprintf($this->_getQrFormat(), $customerCusId, "0000000000", $promotionId, $startTimestamp, $shopCode);
        $encrypttedMsg = $this->_qrCrypt->encrypt($message);
        $qrCode = $this->_qrManager->generateQR($encrypttedMsg, $message, 'bf_storepass', true);
        if($qrCode !== false) return $qrCode;
        else return '';
    }

    /**
     * @return string
     */
    protected function _getQrFormat()
    {
        return "%s-%s-%s-%s-%s";
    }

    /**
     * @return string
     */
    public function getQrAuthToken()
    {
        return (string) $this->scopeConfig->getValue('singular_qr/general/cdn_token');
    }

    /**
     * @return string
     */
    public function getNoLoginUrl()
    {
        $homeUrl = $this->_urlBuilder->getUrl('/');
        $homeUrl = "{$homeUrl}#bflogin";
        return $homeUrl;
    }
}
