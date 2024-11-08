<?php

namespace Hiperdino\Dinitos\Api;

use Hiperdino\Dinitos\Api\Data\HistoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface HistoryRepositoryInterface
{
    public function save(HistoryInterface $object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(HistoryInterface $object);

    public function deleteById($id);

}
