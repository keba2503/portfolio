<?php

namespace Hiperdino\BlackFriday\Controller\Adminhtml\Booking;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResourceConnection;

class ExportAll extends Action
{

    protected $fileFactory;
    protected $directory;
    protected $resourceConnection;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DirectoryList $directory,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->directory = $directory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $path = $this->directory->getPath('var');
        $filename = 'export-blackfriday-bookings.csv';
        $fullPath = "{$path}/{$filename}";
        $fp = fopen($fullPath, 'w');
        foreach ($this->_getBookingExportArray() as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        return $this->fileFactory->create($filename, [
            'type' => 'filename',
            'value' => $fullPath,
            'rm' => true,
        ]);
    }

    /**
     * @return array
     */
    protected function _getBookingExportArray()
    {
        try {
            $csvRows = [
                [
                    "ID Cliente",
                    "Email Cliente",
                    "Tienda",
                    "Código Tienda",
                    "Hora inicio",
                    "Hora fin",
                    "Fecha de la reserva",
                    "Fue reservada el"
                ]
            ];
            $connection = $this->resourceConnection->getConnection();
            $query = "SELECT cus.entity_id AS customer_id, cus.email AS customer_email, st.name AS shop_name, st.store_code AS shop_code, bft.start_time, bft.end_time, bfb.booked_for, bfb.booked_at
                    FROM hiperdino_blackfriday_storepass_booking AS bfb
                    LEFT JOIN hiperdino_blackfriday_storepass_timeslot AS bft ON bfb.timeslot_id = bft.id
                    LEFT JOIN customer_entity AS cus ON bfb.customer_id = cus.entity_id 
                    LEFT JOIN singular_tiendas AS st ON bft.parent_store = st.id";
            $results = $connection->fetchAll($query);
            foreach($results as $result) {
                array_push($csvRows, $result);
            }
            return $csvRows;
        } catch(\Exception $e) {
            return [];
        }
    }
}
