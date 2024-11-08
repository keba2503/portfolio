<?php

namespace Hiperdino\BlackFriday\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StorepassBookingRepositoryInterface
{
    public function save($object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete($object);

    public function deleteById($id);
}
