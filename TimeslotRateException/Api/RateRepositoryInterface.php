<?php

namespace Hiperdino\TimeslotRateException\Api;

use Hiperdino\TimeslotRateException\Api\Data\RateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface RateRepositoryInterface
{
    public function save(RateInterface $object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(RateInterface $object);

    public function deleteById($id);
}
