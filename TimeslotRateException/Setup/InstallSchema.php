<?php

namespace Hiperdino\TimeslotRateException\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('hiperdino_delivery_rate_exception_timeslot');

        if (!$installer->getConnection()->isTableExists($tableName)) {

            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'exception_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Foreign key referencing id from hiperdino_delivery_rate_exception'
                )
                ->addColumn(
                    'timeslot_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Foreign key referencing id from singular_delivery_timeslot'
                )
                ->addIndex(
                    $installer->getIdxName('hiperdino_delivery_rate_exception_timeslot', ['exception_id', 'timeslot_id'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY),
                    ['exception_id', 'timeslot_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
                )
                ->addForeignKey(
                    $installer->getFkName('hiperdino_delivery_rate_exception_timeslot', 'exception_id', 'hiperdino_delivery_rate_exception', 'id'),
                    'exception_id',
                    $installer->getTable('hiperdino_delivery_rate_exception'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('hiperdino_delivery_rate_exception_timeslot', 'timeslot_id', 'singular_delivery_timeslot', 'id'),
                    'timeslot_id',
                    $installer->getTable('singular_delivery_timeslot'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Table for relating timeslots with delivery rate exceptions')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
