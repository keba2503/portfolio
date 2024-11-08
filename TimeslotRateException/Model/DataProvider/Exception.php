<?php

namespace Hiperdino\TimeslotRateException\Model\DataProvider;

use Hiperdino\TimeslotRateException\Model\Data\ExceptionFactory;
use Hiperdino\TimeslotRateException\Model\ResourceModel\Exception\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Exception extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    protected $collection;
    protected DataPersistorInterface $dataPersist;
    protected ExceptionFactory $exceptionFactory;
    protected RequestInterface $request;
    protected ResourceConnection $resourceConnection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ExceptionFactory $exceptionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersist,
        ResourceConnection $resourceConnection,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->exceptionFactory = $exceptionFactory;
        $this->request = $request;
        $this->dataPersist = $dataPersist;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $requestId = $this->request->getParam($this->requestFieldName);
        $exception = $this->exceptionFactory->create()->load($requestId);
        if ($exception->getId()) {
            $transactionData = $exception->getData();
            $transactionData['timeslots'] = $this->getTimeslots($transactionData['id']);
            $this->loadedData[$exception->getId()] = $transactionData;
        }

        return $this->loadedData;
    }

    protected function getTimeslots($id)
    {
        $connection = $this->resourceConnection->getConnection();
        $timeslotsSelect = $connection->fetchAll("SELECT timeslot_id FROM hiperdino_delivery_rate_exception_timeslot WHERE exception_id = {$id}");
        $timeslots = [];
        foreach ($timeslotsSelect as $timeslot) {
            $timeslots[] = $timeslot['timeslot_id'];
        }

        return $timeslots ?: [];
    }
}
