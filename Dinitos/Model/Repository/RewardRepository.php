<?php

namespace Hiperdino\Dinitos\Model\Repository;

use Exception;
use Hiperdino\Dinitos\Api\Data\RewardInterface;
use Hiperdino\Dinitos\Api\RewardRepositoryInterface;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Data\RewardFactory;
use Hiperdino\Dinitos\Model\ResourceModel\Reward;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class RewardRepository implements RewardRepositoryInterface
{
    protected Reward $resourceConnection;
    protected Logger $logger;
    protected RewardFactory $rewardFactory;
    protected CollectionFactory $collectionFactory;
    protected SearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        Reward $resourceConnection,
        Logger $logger,
        RewardFactory $rewardFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->rewardFactory = $rewardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @throws CouldNotSaveException
     * @throws Exception
     */
    public function save(RewardInterface $object): void
    {
        try {
            $object->setStores(implode(',', $object->getStores()));
            $this->resourceConnection->save($object);
        } catch (Exception $e) {
            $this->logger->logDinitosReward("Error guardando la recompensa : \n {$e->getMessage()}");
            throw new Exception(__($e->getMessage()));
        }
    }

    /**
     * @throws Exception
     */
    public function getById($id): RewardInterface
    {
        try {
            $object = $this->rewardFactory->create();
            $this->resourceConnection->load($object, $id, 'id');

            return $object;
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
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
    public function delete(RewardInterface $object): void
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
            $rewards = $this->getById($id);
            $this->delete($rewards);
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
    }
}