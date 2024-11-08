<?php

namespace Hiperdino\TimeslotRateException\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Exception extends AbstractDb
{
    const DUPLICATE_EXCEPTION_IN_RANGE_SQL = "SELECT * FROM hiperdino_delivery_rate_exception hdre WHERE is_active = 1 AND id IN (SELECT exception_id FROM hiperdino_delivery_rate_exception_timeslot WHERE timeslot_id IN (?))";

    protected $connection;

    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->connection = $resourceConnection->getConnection();
    }

    protected function _construct()
    {
        $this->_init('hiperdino_delivery_rate_exception', 'id');
    }

    public function save(AbstractModel $object)
    {
        $timeslots = $object->getTimeslots();
        $this->beforeSave($object);
        $object->unsTimeslots();

        $res = parent::save($object);

        $this->connection->query("DELETE FROM hiperdino_delivery_rate_exception_timeslot WHERE exception_id = {$object->getId()}");
        if (isset($timeslots[0])) {
            foreach ($timeslots as $k => $v) $timeslots[$k] = "(" . $object->getId() . "," . $v . ")";
            $this->connection->query("INSERT INTO hiperdino_delivery_rate_exception_timeslot VALUES " . implode(',', $timeslots ?: []));
        }

        return $res;
    }

    public function beforeSave(DataObject $object)
    {
        parent::beforeSave($object);
        $duplicateRows = $this->getDuplicateRows($object);
        if (\count($duplicateRows) > 0) {
            $exceptionIds = \implode(",", \array_column($duplicateRows, "id"));
            throw new \Exception("Ya hay una excepción para esa franja: " . $exceptionIds);
        }
    }

    /**
     * @param DataObject $object
     * @return array
     */
    protected function getDuplicateRows(DataObject $object): array
    {
        $timeslots = \implode(",", $object->getTimeslots());
        if ($endDate = $this->getDate($object->getData('end_date'))) {
            $startDateSQL = "AND (start_date IS NULL OR start_date <= ?)";
        } else {
            $startDateSQL = "AND (start_date IS NULL OR start_date IS NOT ?)";
        }

        if ($startDate = $this->getDate($object->getData('start_date'))) {
            $endDateSQL = "AND (end_date IS NULL OR end_date <= ?)";
        } else {
            $endDateSQL = "AND (end_date IS NULL OR end_date IS NOT ?)";
        }

        return $this->connection->fetchAll(self::DUPLICATE_EXCEPTION_IN_RANGE_SQL . $startDateSQL . $endDateSQL, [$timeslots, $endDate, $startDate]);
    }

    protected function getDate($date)
    {
        return $date ? date_create_from_format('d/m/Y', $date)->format('Y-m-d') : null;
    }
}
