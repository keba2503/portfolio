<?php

namespace Hiperdino\Anniversary2020\Helper;

use Exception;
use Magento\Framework\App\ResourceConnection;

class Import
{
    protected ResourceConnection $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection,
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $file
     * @throws Exception
     */
    public function importDataFromCsv($file)
    {
        if (($resource = fopen($file, "r")) !== FALSE) {
            $connection = $this->resourceConnection->getConnection();
            while (($row = fgetcsv($resource, 1000, ";")) !== FALSE) {
                try {
                    if (count($row) != 1) {
                        continue;
                    }
                    $rascaCode = $row[0];

                    $query = "INSERT INTO hiperdino_anniversary2020_rascas (rasca_code)
                        VALUES ('{$rascaCode}')";

                    $connection->query($query);
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
            fclose($resource);
        } else {
            throw new Exception('No se ha podido abrir el archivo CSV.');
        }
    }
}
