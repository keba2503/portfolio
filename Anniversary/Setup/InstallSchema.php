<?php

namespace Hiperdino\Anniversary\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('hiperdino_anniversary_product');
        if (!$installer->getConnection()->isTableExists($tableName)) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'product_sku',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Sku'
                )
                ->addColumn(
                    'store',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Tienda'
                )
                ->addColumn(
                    'from_date',
                    Table::TYPE_DATETIME,
                    null,
                    [],
                    'Fecha inicio'
                )
                ->addColumn(
                    'to_date',
                    Table::TYPE_DATETIME,
                    null,
                    [],
                    'Fecha fin'
                )
                ->addColumn(
                    'processed',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Si está procesado'
                )
                ->addColumn(
                    'is_active',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Si se procesó bien'
                )
                ->setComment('Tabla de productos de aniversario')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}