<?php

namespace Hiperdino\BlackFriday\Helper;

class Import
{

    protected $_resourceConnection;
    protected $_storeRepository;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreRepository $storeRepository
    ) {
        $this->_resourceConnection = $resourceConnection;
        $this->_storeRepository = $storeRepository;
    }

    /**
     * @param string $file
     * @throws \Exception
     */
    public function importDataFromCsv($file)
    {
        if(($resource = fopen($file, "r")) !== FALSE) {
            $connection = $this->_resourceConnection->getConnection();
            while (($row = fgetcsv($resource, 1000, ";")) !== FALSE) {
                try {
                    if(count($row) != 4 && count($row) != 5) {
                        continue;
                    }
                    $sku = $row[0];
                    if(! is_numeric($sku)) {
                        continue;
                    }
                    $tienda = $row[1];
                    $origDesde = $row[2];
                    $origHasta = $row[3];

                    if(isset($row[4]) && is_numeric($row[4])) {
                        $maxCantidad = $row[4];
                    } else {
                        $maxCantidad = 0;
                    }

                    $productSku = str_pad($sku, 18, '0', STR_PAD_LEFT);
                    $store = "c{$tienda}";
                    $desde = date_create_from_format('d/m/y', $origDesde);
                    if(! $desde) $desde = date_create_from_format('d/m/Y', $origDesde);
                    $hasta = date_create_from_format('d/m/y', $origHasta);
                    if(! $hasta) $hasta = date_create_from_format('d/m/Y', $origHasta);
                    if (! $desde || !$hasta) continue;
                    $fromDate = $desde->format('Y-m-d 00:00:00');
                    $toDate = $hasta->format('Y-m-d 00:00:00');
                    $active = 0;

                    // Insertamos
                    $query = "INSERT INTO hiperdino_blackfriday_product (product_sku, store,from_date,to_date,max_buy_qty) 
                              VALUES ('{$productSku}','{$store}','{$fromDate}','{$toDate}','{$maxCantidad}')";
                    $connection->query($query);
                } catch(\Exception $e) {
                    fclose($resource);
                    throw $e;
                }
            }
            fclose($resource);
        } else {
            throw new \Exception('No se ha podido abrir el archivo CSV.');
        }
    }
}
