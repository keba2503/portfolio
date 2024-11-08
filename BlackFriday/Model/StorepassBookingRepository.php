<?php

namespace Hiperdino\BlackFriday\Model;

use Exception;
use Hiperdino\BlackFriday\Api\StorepassBookingRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Hiperdino\BlackFriday\Model\StorepassBookingFactory;
use Hiperdino\BlackFriday\Model\ResourceModel\StorepassBooking\CollectionFactory;

class StorepassBookingRepository implements StorepassBookingRepositoryInterface
{
    protected $objectFactory;
    protected $collectionFactory;
    protected $searchResultsFactory;
    protected $connection;
    protected $searchCriteriaBuilder;

    public function __construct(
        StorepassBookingFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ResourceConnection $resourceConnection,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->connection = $resourceConnection->getConnection();
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function save($object)
    {
        try {
            $object->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $object;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    public function delete($object)
    {
        try {
            $object->delete();
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

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

    /**
     * @param array $data
     * @return \Hiperdino\BlackFriday\Model\StorepassBooking
     */
    public function create($data = [])
    {
        $object = $this->objectFactory->create();
        $object->addData($data);
        return $object;
    }

    /**
     * @param mixed $customerId
     * @return \Hiperdino\BlackFriday\Model\StorepassBooking
     * @throws NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        $object = $this->objectFactory->create();
        $object->load($customerId, 'customer_id');
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with customer_id "%1" does not exist.', $id));
        }
        return $object;
    }
}
