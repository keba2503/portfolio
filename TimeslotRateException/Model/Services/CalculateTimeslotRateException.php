<?php

namespace Hiperdino\TimeslotRateException\Model\Services;

use Hiperdino\TimeslotRateException\Api\Data\RateInterface;
use Hiperdino\TimeslotRateException\Api\ExceptionRepositoryInterface;
use Hiperdino\TimeslotRateException\Helper\Config;
use Hiperdino\TimeslotRateException\Model\Data\Exception;

class CalculateTimeslotRateException
{
    public function __construct(
        protected readonly ExceptionRepositoryInterface $timeslotRateExceptionRepository,
        protected readonly Config $config
    ) {
    }

    public function execute($timeslotId, $date)
    {
        /** @var Exception $timeslotRateException */
        $timeslotRateException = $this->timeslotRateExceptionRepository->getByActiveTimeslotId($timeslotId, $date);

        return $this->getRatePrice($timeslotRateException);
    }

    /**
     * @param Exception|bool $timeslotRateException
     * @return string|null
     */
    protected function getRatePrice(Exception|bool $timeslotRateException): ?string
    {
        if ($timeslotRateException) {
            /** @var RateInterface $rate */
            $rate = $timeslotRateException->getRateInfo();
            if ($rate && $rate->getId()) {
                return $rate->getAmount();
            }
        }

        return null;
    }
}
