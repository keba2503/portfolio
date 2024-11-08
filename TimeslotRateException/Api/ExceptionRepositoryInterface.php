<?php

namespace Hiperdino\TimeslotRateException\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Hiperdino\TimeslotRateException\Api\Data\ExceptionInterface;

interface ExceptionRepositoryInterface
{
    public function save(ExceptionInterface $object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(ExceptionInterface $object);

    public function deleteById($id);

    public function getByActiveTimeslotId($timeslotId, $date);
}
