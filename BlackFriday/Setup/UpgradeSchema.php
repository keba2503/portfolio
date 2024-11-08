<?php

namespace Hiperdino\BlackFriday\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $this->_upgrade_1_0_3($setup);
        }
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $this->_upgrade_1_0_4($setup);
        }
        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $this->_upgrade_1_0_5($setup);
        }
    }

    protected function _upgrade_1_0_3(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->addColumn(
            $installer->getTable('hiperdino_blackfriday_product'),
            'max_buy_qty',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => '255',
                'comment' => 'Cantidad máxima de compra por tienda'
            ]
        );
        $installer->endSetup();
    }

    protected function _upgrade_1_0_4(SchemaSetupInterface $setup)
    {
        // Tabla hiperdino_blackfriday_storepass_timeslot
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_timeslot');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
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
                    'ID'
                )
                ->addColumn(
                    'limit',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Límite de reservas'
                )
                ->addColumn(
                    'parent_store',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Tienda asociada'
                )
                ->addColumn(
                    'start_time',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Hora inicio'
                )
                ->addColumn(
                    'end_time',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Hora fin'
                )
                ->addColumn(
                    'weekdays',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Dias de la semana'
                )
                ->addColumn(
                    'is_active',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Activo'
                )
                ->addForeignKey(
                    $setup->getFkName('hiperdino_blackfriday_storepass_timeslot', 'parent_store', 'singular_tiendas', 'id'),
                    'parent_store',
                    $setup->getTable('singular_tiendas'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Hiperdino BlackFriday StorePass Timeslot Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }

        // Tabla hiperdino_blackfriday_storepass_booking
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_booking');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
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
                    'ID'
                )
                ->addColumn(
                    'timeslot_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Franja asociada'
                )
                ->addColumn(
                    'people',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 1],
                    'Nº de personas'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Cliente asociado'
                )
                ->addColumn(
                    'booked_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Momento de realizacion de la reserva'
                )
                ->addColumn(
                    'qr',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Qr asociado'
                )
                ->addForeignKey(
                    $setup->getFkName('hiperdino_blackfriday_storepass_booking', 'timeslot_id', 'hiperdino_blackfriday_storepass_timeslot', 'id'),
                    'timeslot_id',
                    $setup->getTable('hiperdino_blackfriday_storepass_timeslot'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('hiperdino_blackfriday_storepass_booking', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Hiperdino BlackFriday StorePass Booking Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }

        // Índice único customer_id en la tabla hiperdino_blackfriday_storepass_booking
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_booking');
        if ($setup->getConnection()->isTableExists($tableName)) {
            $setup->getConnection()
                ->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        'hiperdino_blackfriday_storepass_booking',
                        'customer_id',
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'customer_id',
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
        }

        // Columna turn en la tabla hiperdino_blackfriday_storepass_booking
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_booking');
        if ($setup->getConnection()->isTableExists($tableName)) {
            $columns = [
                'turn_number' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'size' => 255,
                    'nullable' => false,
                    'comment' => 'Número de turno',
                    'default' => 0
                ]
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        // Tabla hiperdino_blackfriday_storepass_turn
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_turn');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'turn',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Turn'
                )
                ->setComment('Hiperdino BlackFriday StorePass Turn Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }
    }

    protected function _upgrade_1_0_5(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('hiperdino_blackfriday_storepass_booking');
        if ($setup->getConnection()->isTableExists($tableName)) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()->addColumn(
                $tableName,
                'booked_for',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Fecha para la que se ha reservado la franja horaria.'
                ]
            );
            $installer->endSetup();
        }
    }
}
