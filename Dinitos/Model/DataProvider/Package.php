<?php

namespace Hiperdino\Dinitos\Model\DataProvider;

use Exception;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Hiperdino\Dinitos\Model\ResourceModel\Package\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Package extends AbstractDataProvider
{
    protected PackageRepository $packageRepository;
    protected CollectionFactory $collectionFactory;
    protected RequestInterface $request;
    protected array $loadedData = [];
    protected $collection;

    public function __construct(
        PackageRepository $packageRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->packageRepository = $packageRepository;
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @throws Exception
     */
    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }
        $requestId = $this->request->getParam($this->requestFieldName);
        $object = $this->packageRepository->getById($requestId);
        if ($object->getId()) {
            $dinitosData = $object->getData();
            $this->loadedData[$object->getId()] = $dinitosData;
        }

        return $this->loadedData;
    }
}
