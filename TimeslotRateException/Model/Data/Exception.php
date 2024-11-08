<?php

namespace Hiperdino\TimeslotRateException\Model\Data;

use Hiperdino\TimeslotRateException\Api\Data\ExceptionInterface;
use Hiperdino\TimeslotRateException\Api\RateRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Exception extends AbstractModel implements IdentityInterface, ExceptionInterface
{
    const CACHE_TAG = 'hiperdino_timeslotRateException_exception';
    protected $_cacheTag = 'hiperdino_timeslotRateException_exception';
    protected $_eventPrefix = 'hiperdino_timeslotRateException_exception';

    protected function _construct()
    {
        $this->_init('Hiperdino\TimeslotRateException\Model\ResourceModel\Exception');
    }

    public function __construct(
        Context $context,
        Registry $registry,
        protected readonly RateRepositoryInterface $rateRepository
    ) {
        parent::__construct($context, $registry);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    public function setStartDate($startDate)
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    public function getEndDate()
    {
        return $this->getData(self::END_DATE);
    }

    public function setEndDate($endDate)
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    public function getTimeslots()
    {
        return $this->getData(self::TIMESLOT);
    }

    public function setTimeslots($timeslots)
    {
        return $this->setData(self::TIMESLOT, $timeslots);
    }

    public function getRate()
    {
        return $this->getData(self::RATE);
    }

    public function getRateInfo()
    {
        if ($this->getData(self::RATE) !== null) {
            try {
                return $this->rateRepository->getById($this->getData(self::RATE));
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    public function setRate($rate)
    {
        return $this->setData(self::RATE, $rate);
    }

    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}
