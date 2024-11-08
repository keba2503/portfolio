<?php

namespace Hiperdino\Dinitos\Model\Services\History;

use Exception;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Hiperdino\Dinitos\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GetMovementsCustomer
{
    const ACCUMULATION_MOVEMENT = "0";

    protected Logger $logger;
    protected CollectionFactory $collectionFactory;
    protected PackageRepository $packageRepository;

    public function __construct(
        Logger $logger,
        CollectionFactory $collectionFactory,
        PackageRepository $packageRepository
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->packageRepository = $packageRepository;
    }

    /**
     *
     * @param string $customerId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function callHistoryByCustomer(string $customerId): array
    {
        try {
            $historyCollection = $this->collectionFactory->create();
            $historyCollection->addFieldToFilter('customer_id', $customerId);
            $historyCollection->setOrder('id', 'DESC');
            $history = $historyCollection->getData();

            foreach ($history as $key => $historyElement) {
                if ($historyElement['transaction_type'] === self::ACCUMULATION_MOVEMENT) {
                    $packageId = $historyElement['package_id'];
                    $package = $this->packageRepository->getById($packageId);
                    $expirationDate = $package->getExpirationDate();
                    $historyElement['expiration_date'] = $expirationDate;
                    $history[$key] = $historyElement;
                }
                if ($historyElement['dinitos_quantity'] < 0) {
                    $history[$key]['dinitos_quantity'] = abs($historyElement['dinitos_quantity']);
                }
            }

            return $history;
        } catch (Exception $e) {
            $this->logger->logHistoryDinitos("Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function callHistoryByIncrementId($customerId, $incrementId): array
    {
        try {
            $historyCollection = $this->collectionFactory->create();
            $historyCollection->addCustomerAndIncrementIdFilter($customerId, $incrementId);
            $historyCollection->setOrder('id', 'DESC');

            return $historyCollection->getData();
        } catch (Exception $e) {
            $this->logger->logHistoryDinitos("Error: " . $e->getMessage());
            throw $e;
        }
    }
}