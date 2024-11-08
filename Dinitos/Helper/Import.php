<?php

namespace Hiperdino\Dinitos\Helper;

use DateTime;
use Exception;
use Magento\Framework\App\ResourceConnection;

class Import
{
    protected ResourceConnection $resourceConnection;
    protected Logger $logger;

    public function __construct(
        ResourceConnection $resourceConnection,
        Logger $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function importDataFromCsv(string $file): void
    {
        $errors = [];
        $count = 1;
        if (($resource = fopen($file, "r")) !== FALSE) {
            $connection = $this->resourceConnection->getConnection();
            while (($row = fgetcsv($resource, 1000, ";")) !== FALSE) {
                try {
                    if (count($row) != 6) {
                        $errors[] = "Error en la fila: " . $count . ", no hay 6 datos en la fila, puede ser que no esté separado por ;?";
                        continue;
                    }

                    $sku = $row[0];
                    if (!is_numeric($sku)) {
                        $errors[] = "Error en la fila: " . $count . ", el sku del producto no es numérico";
                        continue;
                    }
                    $dinitos = (int)$row[1];
                    $grams = $row[2] ? 1 : 0;
                    $store = "c{$row[3]}";
                    $from = $this->getDate($row[4]);
                    $to = $this->getDate($row[5]);
                    $productSku = str_pad($sku, 18, '0', STR_PAD_LEFT);

                    if (!$from || !$to) {
                        $errors[] = "Error en la fila: " . $count . ", no se puede procesar la fecha, por favor revise el formato";
                        continue;
                    }
                    $fromDate = $from->format('Y-m-d 00:00:00');
                    $toDate = $to->format('Y-m-d 00:00:00');
                    $active = (int)(time() < $from->getTimestamp());

                    $query = "INSERT INTO hiperdino_dinitos (product_sku,store,is_active,dinitos,gramos,from_date,to_date) 
                              VALUES ('{$productSku}','{$store}',{$active},{$dinitos},{$grams},'{$fromDate}','{$toDate}')";

                    $connection->query($query);
                    $count++;
                } catch (Exception $e) {
                    fclose($resource);
                    $this->logger->log("Error al procesar CSV: " . $e->getMessage());
                    throw $e;
                }
            }
            fclose($resource);
            if (count($errors)) {
                $message = "No se ha importado el fichero debido a estos errores: \n";
                $message .= implode("\n", $errors);
                throw new Exception($message);
            }
        } else {
            $this->logger->log("No se ha podido abrir el archivo CSV: $file");
            throw new Exception('No se ha podido abrir el archivo CSV.');
        }
    }

    /**
     * @param mixed $date
     * @return DateTime|false
     */
    protected function getDate(mixed $date): false|DateTime
    {
        $formattedDate = date_create_from_format('d/m/y', $date);
        if (!$formattedDate) {
            $formattedDate = date_create_from_format('d/m/Y', $date);
        }

        return $formattedDate;
    }
}