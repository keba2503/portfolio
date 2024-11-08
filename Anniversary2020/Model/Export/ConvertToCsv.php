<?php

namespace Hiperdino\Anniversary2020\Model\Export;

use Exception;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd\CollectionFactory;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToCsv as ConvertToCsvParent;
use Magento\Ui\Model\Export\MetadataProvider;
use Singular\Islands\Model\IslandsRepository;

/**
 * Class ConvertToCsv
 */
class ConvertToCsv extends ConvertToCsvParent
{
    const DELETED_CUSTOMER_DATA = "CLIENTE ELIMINADO";

    protected RegisterParticipation $registerParticipation;
    protected $directory;
    protected $metadataProvider;
    protected $filter;
    protected CustomerRepositoryInterface $customerRepository;
    protected IslandsRepository $islandsRepository;
    protected $pageSize = null;
    protected Logger $log;
    protected CollectionFactory $collectionFactory;

    /**
     * @param RegisterParticipation $registerParticipation
     * @param CustomerRepositoryInterface $customerRepository
     * @param IslandsRepository $islandsRepository
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param Logger $log
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        RegisterParticipation $registerParticipation,
        CustomerRepositoryInterface $customerRepository,
        IslandsRepository $islandsRepository,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        Logger $log,
        CollectionFactory $collectionFactory,
        int $pageSize = 200
    ) {
        $this->registerParticipation = $registerParticipation;
        $this->customerRepository = $customerRepository;
        $this->islandsRepository = $islandsRepository;
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
        $this->log = $log;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);

    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();
        if ($component->getName() != "hiperdino_anniversary2020_post_rascas_listing") {
            return parent::getCsvFile();
        }

        $this->log->Log("Se inicia la exportacion en archivo csv");
        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->getAllHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = $dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            if ($component->getName() == 'hiperdino_anniversary2020_post_rascas_listing') {
                foreach ($items as $item) {
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->getAllRowData($item, $fields, $options));
                }
            } else {
                foreach ($items as $item) {
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }

    /**
     * @throws Exception
     */
    private function getAllHeaders($component)
    {
        $allHeaders = $this->metadataProvider->getHeaders($component);

        if ($component->getName() == 'hiperdino_anniversary2020_post_rascas_listing') {
            $allHeaders = $this->getExtraCustomerHeader($allHeaders);
        }

        return $allHeaders;
    }

    private function getExtraCustomerHeader($allHeaders)
    {
        $allHeaders[] = 'Nombre';
        $allHeaders[] = 'Apellidos';
        $allHeaders[] = 'Correo';
        $allHeaders[] = 'DNI';
        $allHeaders[] = 'Teléfono';
        $allHeaders[] = 'Isla';

        return $allHeaders;
    }

    private function getAllRowData($item, $fields, $options)
    {
        $allRowData = $this->metadataProvider->getRowData($item, $fields, $options);

        $customerIdKey = array_search('customer_id', $fields);
        if ($customerIdKey) {
            $customerId = $allRowData[$customerIdKey];
            if ($customerId) {
                return $this->addExtraCustomerRowData($allRowData, $customerId);
            }
        }

        return $this->addWhiteRowData($allRowData);
    }

    private function addExtraCustomerRowData($allRowData, $customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);

            $raffleRgpdCollection = $this->collectionFactory->create();
            $raffleRgpdCollection->addFieldToFilter('customer_id', $customerId);

            $firstItem = $raffleRgpdCollection->getFirstItem();
            $taxvat = $firstItem->getData('taxvat');
            $customerTelephone = $firstItem->getData('phone');


            $allRowData[] = $customer->getFirstname();
            $allRowData[] = $customer->getLastname();
            $allRowData[] = $customer->getEmail();
            $allRowData[] = $taxvat;
            $allRowData[] = $customerTelephone;

            $island = '';
            try {
                $islandId = $firstItem->getData('island');
                if ($islandId) {
                    $island = $this->islandsRepository->getById($islandId);
                    if ($island) {
                        $island = $island->getName() ? $island->getName() : '';
                    }
                }
            } catch (Exception $e) {
            }

            $allRowData[] = $island;
        } catch (\Exception $e) {
            $allRowData[] = self::DELETED_CUSTOMER_DATA;
        }

        return $allRowData;
    }

    private function addWhiteRowData($allRowData)
    {
        $extraCustomerHeaders = $this->getExtraCustomerHeader([]);
        foreach ($extraCustomerHeaders as $extraCustomerHeader) {
            $allRowData[] = '';
        }

        return $allRowData;
    }
}
