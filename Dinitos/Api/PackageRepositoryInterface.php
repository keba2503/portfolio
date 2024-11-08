<?php

namespace Hiperdino\Dinitos\Api;

use Hiperdino\Dinitos\Api\Data\PackageInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PackageRepositoryInterface
{
    public function save(PackageInterface $object);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(PackageInterface $object);

    public function deleteById($id);
}
