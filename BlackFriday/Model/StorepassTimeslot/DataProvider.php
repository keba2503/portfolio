<?php

namespace Hiperdino\BlackFriday\Model\StorepassTimeslot;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Hiperdino\BlackFriday\Model\ResourceModel\StorepassTimeslot\Collection;
use Hiperdino\BlackFriday\Model\ResourceModel\StorepassTimeslot\CollectionFactory;
use Hiperdino\BlackFriday\Model\StorepassTimeslotFactory;
use Singular\Delivery\Ui\Component\Listing\Column\Weekday\Options;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    protected $_timeslotFactory;
    protected $_request;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $timeslotCollectionFactory
     * @param StorepassTimeslotFactory $timeslotFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $timeslotCollectionFactory,
        StorepassTimeslotFactory $timeslotFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $timeslotCollectionFactory->create();
        $this->_timeslotFactory = $timeslotFactory;
        $this->_request = $request;
        $this->dataPersistor = $dataPersistor;
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

        $requestId = $this->_request->getParam($this->requestFieldName);
        $timeslot = $this->_timeslotFactory->create()->load($requestId);
        if ($timeslot->getId()) {
            $timeslotData = $timeslot->getData();
            $timeslotData['weekdays'] = $this->translateWeekdays($timeslotData['weekdays']);
            $this->loadedData[$timeslot->getId()] = $timeslotData;
        }

        return $this->loadedData;
    }

    protected function translateWeekdays($str)
    {
        $res = explode(',', $str ?: "");
        foreach ($res as $k => $v) $res[$k] = Options::$weekdays[$v];

        return implode(',', $res ?: []);
    }
}
