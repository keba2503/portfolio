<?php

namespace Hiperdino\Anniversary\Helper;

use DateTime;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\StoreRepository;

class Import
{
    protected ResourceConnection $resourceConnection;
    protected StoreRepository $storeRepository;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreRepository $storeRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function importDataFromCsv(string $file)
    {
        $resource = fopen($file, "r");
        if (!$resource) {
            throw new Exception('No se ha podido abrir el archivo CSV.');
        }

        $connection = $this->resourceConnection->getConnection();

        while (($row = fgetcsv($resource, 1000, ";")) !== false) {
            try {
                if (!$this->isValidRow($row)) {
                    continue;
                }

                $productSku = str_pad($row[0], 18, '0', STR_PAD_LEFT);
                $store = "c{$row[1]}";

                $fromDate = $this->parseDate($row[2]);
                $toDate = $this->parseDate($row[3]);

                if (!$fromDate || !$toDate) {
                    continue;
                }

                $formattedFromDate = $fromDate->format('Y-m-d 00:00:00');
                $formattedToDate = $toDate->format('Y-m-d 00:00:00');

                $this->insertDataToDatabase($productSku, $store, $formattedFromDate, $formattedToDate, $connection);
            } catch (Exception $e) {
                fclose($resource);
                throw $e;
            }
        }

        fclose($resource);
    }

    /**
     * Validate if a row is valid based on the number of columns and SKU being numeric.
     *
     * @param array $row
     * @return bool
     */
    protected function isValidRow(array $row): bool
    {
        if (count($row) != 4 || !is_numeric($row[0])) {
            return false;
        }

        return true;
    }

    /**
     * Parse a date from the given string and return a DateTime object.
     *
     * @param string $dateString
     * @return DateTime|null
     */
    protected function parseDate(string $dateString): ?DateTime
    {
        $date = date_create_from_format('d/m/y', $dateString);
        if (!$date) {
            $date = date_create_from_format('d/m/Y', $dateString);
        }

        return $date;
    }

    /**
     * Insert the data into the database.
     *
     * @param string $productSku
     * @param string $store
     * @param string $formattedFromDate
     * @param string $formattedToDate
     * @param AdapterInterface $connection
     */
    protected function insertDataToDatabase(string $productSku, string $store, string $formattedFromDate, string $formattedToDate, AdapterInterface $connection)
    {
        $query = "INSERT INTO hiperdino_anniversary_product (product_sku, store, from_date, to_date)
                  VALUES (?, ?, ?, ?)";

        $connection->query($query, [$productSku, $store, $formattedFromDate, $formattedToDate]);
    }
}
