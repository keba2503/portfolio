<?php

namespace Hiperdino\Anniversary\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected AdapterInterface $connection;

    /**
     * @inheritdoc
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->_upgrade101($setup);
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->_upgrade102($setup);
        }
        $setup->endSetup();
    }

    protected function _upgrade101(SchemaSetupInterface $setup)
    {

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'anniversary_total_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario. Rasca y gana obtenidos'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_item'),
            'is_anniversary_product',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Aniversario. Producto de aniversario'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'anniversary_total_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario. Rasca y gana obtenidos'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_item'),
            'is_anniversary_product',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Aniversario. Producto de aniversario'
            ]
        );

    }

    protected function _upgrade102(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'anniversary_extra_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario Extra Qty'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'anniversary_extra_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario Extra Qty'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'anniversary_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario Qty'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'anniversary_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aniversario Qty'
            ]
        );
    }
}
