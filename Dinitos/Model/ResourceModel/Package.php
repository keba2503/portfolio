<?php

namespace Hiperdino\Dinitos\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Package extends AbstractDb
{
    protected AdapterInterface $connection;

    public function __construct(
        Context $context,
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init('hiperdino_dinitos_packages', 'id');
    }
}