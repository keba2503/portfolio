<?php

namespace Hiperdino\TimeslotRateException\Model\Repository;

use Exception as LogException;
use Hiperdino\Coupons\Api\Data\IssuanceInterface;
use Hiperdino\TimeslotRateException\Api\Data\ExceptionInterface;
use Hiperdino\TimeslotRateException\Api\ExceptionRepositoryInterface;
use Hiperdino\TimeslotRateException\Model\Data\ExceptionFactory;
use Hiperdino\TimeslotRateException\Model\ResourceModel\Exception\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExceptionRepository implements ExceptionRepositoryInterface
{
    protected ExceptionFactory $objectFactory;
    protected CollectionFactory $collectionFactory;
    protected SearchResultsInterfaceFactory $searchResultsFactory;
    protected AdapterInterface $connection;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected FilterBuilder $filterBuilder;
    protected FilterGroupBuilder $filterGroupBuilder;

    public function __construct(
        ExceptionFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ResourceConnection $resourceConnection,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->connection = $resourceConnection->getConnection();
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(ExceptionInterface $object)
    {
        try {
            $object->save();
        } catch (LogException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $object;
    }

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(ExceptionInterface $object): bool
    {
        try {
            $object->delete();
        } catch (LogException $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }

        return $object;
    }

    public function getList(SearchCriteriaInterface $criteria)
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
            /** @var SortOrder $sortOrder */
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

    public function getByActiveTimeslotId($timeslotId, $date)
    {
        $criteria = $this->searchCriteriaBuilder;

        $startDateFilter = $this->dateFilter($date, ExceptionInterface::START_DATE, 'lteq');
        $endDateFilter = $this->dateFilter($date, ExceptionInterface::END_DATE, 'gteq');

        $criteria->setFilterGroups([$startDateFilter, $endDateFilter])
            ->addFilter(ExceptionInterface::IS_ACTIVE, true)
            ->addFilter(ExceptionInterface::TIMESLOT, $timeslotId, 'in');
        $items = $this->getList($criteria->create())->getItems();

        if (count($items) != 1) {
            return false;
        }

        return $items[0];
    }

    protected function dateFilter(string $dateValue, string $field, string $condition)
    {
        $dateNull = $this->filterBuilder->setField($field)
            ->setConditionType('null')
            ->create();
        $dateCondition = $this->filterBuilder->setField($field)
            ->setValue($dateValue)
            ->setConditionType($condition)
            ->create();

        return $this->filterGroupBuilder
            ->addFilter($dateNull)
            ->addFilter($dateCondition)
            ->create();
    }
}
