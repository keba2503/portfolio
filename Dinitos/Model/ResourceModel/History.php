<?php

namespace Hiperdino\Dinitos\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class History extends AbstractDb
{
    protected AdapterInterface $connection;

    public function __construct(
        Context $context,
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('hiperdino_dinitos_history', 'id');
    }
}
