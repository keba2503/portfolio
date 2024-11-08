<?php

namespace Hiperdino\Anniversary2020\Api;

use Hiperdino\Anniversary2020\Api\Data\RaffleRgpdInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface RaffleRgpdRepositoryInterface
{
    public function save(RaffleRgpdInterface $object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(RaffleRgpdInterface $object);

    public function deleteById($id);
}
