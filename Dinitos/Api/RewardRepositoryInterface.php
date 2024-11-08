<?php

namespace Hiperdino\Dinitos\Api;

use Hiperdino\Dinitos\Api\Data\RewardInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface RewardRepositoryInterface
{
    public function save(RewardInterface $object);

    public function getById($id): RewardInterface;

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(RewardInterface $object);

    public function deleteById($id);
}