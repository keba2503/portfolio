<?php

namespace Hiperdino\Anniversary2020\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('hiperdino_anniversary2020_rascas');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
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
                    'rasca_code',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                        'unique' => true,
                        'required' => true
                    ],
                    'Código rasca'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Customer Id'
                )
                ->addColumn(
                    'date',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Fecha customer registra rasca'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'hiperdino_anniversary2020_rascas',
                        ['rasca_code'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['rasca_code'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Tabla de rascas de aniversario')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}