<?php

namespace Hiperdino\TimeslotRateException\Api\Data;

interface ExceptionInterface
{
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const TIMESLOT = 'timeslots';
    const RATE = 'rate';
    const IS_ACTIVE = 'is_active';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getStartDate();

    /**
     * @param string $startDate
     * @return $this
     */
    public function setStartDate($startDate);

    /**
     * @return string
     */
    public function getEndDate();

    /**
     * @param string $endDate
     * @return $this
     */
    public function setEndDate($endDate);

    /**
     * @return string
     */
    public function getTimeslots();

    /**
     * @param string $timeslots
     * @return $this
     */
    public function setTimeslots($timeslots);

    /**
     * @return string
     */
    public function getRate();

    /**
     * @param string $rate
     * @return $this
     */
    public function setRate($rate);

    /**
     * @return bool
     */
    public function getIsActive();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);
}
