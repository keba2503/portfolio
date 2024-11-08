<?php

namespace Hiperdino\TimeslotRateException\Model\DataProvider;

use Hiperdino\TimeslotRateException\Model\Data\RateFactory;
use Hiperdino\TimeslotRateException\Model\ResourceModel\Rate\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Rate extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    protected $collection;
    protected DataPersistorInterface $dataPersist;
    protected RateFactory $rateFactory;
    protected RequestInterface $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RateFactory $rateFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersist,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->rateFactory = $rateFactory;
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
        $transaction = $this->rateFactory->create()->load($requestId);
        if ($transaction->getId()) {
            $transactionData = $transaction->getData();
            $this->loadedData[$transaction->getId()] = $transactionData;
        }

        return $this->loadedData;
    }
}
