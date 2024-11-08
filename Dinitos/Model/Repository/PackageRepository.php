<?php

namespace Hiperdino\Dinitos\Model\Repository;

use Exception;
use Hiperdino\Dinitos\Api\Data\PackageInterface;
use Hiperdino\Dinitos\Api\PackageRepositoryInterface;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Data\PackageFactory;
use Hiperdino\Dinitos\Model\ResourceModel\Package as PackageResource;
use Hiperdino\Dinitos\Model\ResourceModel\Package\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class PackageRepository implements PackageRepositoryInterface
{
    public function __construct(
        protected PackageResource $resourceConnection,
        protected Logger $logger,
        protected PackageFactory $packageFactory,
        protected CollectionFactory $collectionFactory,
        protected SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    /**
     * @throws CouldNotSaveException
     * @throws Exception
     */
    public function save(PackageInterface $object): void
    {
        try {
            $this->resourceConnection->save($object);
        } catch (Exception $e) {
            $this->logger->logDinitosReward("Error guardando la recompensa : \n {$e->getMessage()}");
            throw new Exception(__($e->getMessage()));
        }
    }

    /**
     * @throws Exception
     */
    public function getById($id): PackageInterface
    {
        try {
            $object = $this->packageFactory->create();
            $this->resourceConnection->load($object, $id, 'id');

            return $object;
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
    }

    public function getList(SearchCriteriaInterface $criteria): \Magento\Framework\Api\SearchResultsInterface
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @throws Exception
     */
    public function delete(PackageInterface $object): void
    {
        try {
            $this->resourceConnection->delete($object);
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
    }

    /**
     * @throws Exception
     */
    public function deleteById($id): void
    {
        try {
            $dinitosPackage = $this->getById($id);
            $this->delete($dinitosPackage);
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
    }
}