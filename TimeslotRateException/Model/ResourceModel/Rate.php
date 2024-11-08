<?php

namespace Hiperdino\TimeslotRateException\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Rate extends AbstractDb
{
    protected AdapterInterface $connection;

    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->connection = $resourceConnection->getConnection();
    }

    protected function _construct()
    {
        $this->_init('hiperdino_delivery_rate', 'id');
    }
}
