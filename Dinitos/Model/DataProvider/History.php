<?php

namespace Hiperdino\Dinitos\Model\DataProvider;

use Hiperdino\Dinitos\Model\Data\HistoryFactory;
use Hiperdino\Dinitos\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class History extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    protected $collection;
    protected DataPersistorInterface $dataPersist;
    protected HistoryFactory $historyFactory;
    protected RequestInterface $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        HistoryFactory $historyFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersist,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->historyFactory = $historyFactory;
        $this->request = $request;
        $this->dataPersist = $dataPersist;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        $transaction = $this->historyFactory->create()->load($requestId);
        if ($transaction->getId()) {
            $transactionData = $transaction->getData();
            $this->loadedData[$transaction->getId()] = $transactionData;
        }

        return $this->loadedData;
    }
}
