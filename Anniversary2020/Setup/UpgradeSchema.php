<?php

namespace Hiperdino\Anniversary2020\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.6') < 0) $this->_upgrade101($setup);
        if (version_compare($context->getVersion(), '0.0.7') < 0) $this->_upgrade102($setup);
        if (version_compare($context->getVersion(), '0.0.8') < 0) $this->_upgrade103($setup);
        if (version_compare($context->getVersion(), '0.0.9') < 0) $this->_upgrade104($setup);
        if (version_compare($context->getVersion(), '0.1.0') < 0) $this->_upgrade105($setup);

    }

    protected function _upgrade101(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->getConnection()->isTableExists('hiperdino_participation_queue')) {
            $table = $installer->getConnection()->newTable($installer->getTable('hiperdino_participation_queue'))
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
                    'body',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Body'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Creation Time'
                )
                ->addColumn(
                    'tries',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'message',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Message'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Type'
                )
                ->setComment('Participation Queue Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

    protected function _upgrade102(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->getConnection()->isTableExists('hiperdino_raffle_rgpd')) {
            $table = $installer->getConnection()->newTable($installer->getTable('hiperdino_raffle_rgpd'))
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
                    'taxvat',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'taxvat'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'customer_id'
                )
                ->addColumn(
                    'phone',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'phone'
                )
                ->addColumn(
                    'island',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'island'
                )
                ->addColumn(
                    'accept_rgpd',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'accept_rgpd'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Creation Time'
                )
                ->setComment('RGPD Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

    protected function _upgrade103(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$setup->getConnection()->tableColumnExists("hiperdino_participation_queue", "response")) {
            $setup->getConnection()->addColumn(
                "hiperdino_participation_queue",
                "response",
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => null,
                    'comment' => 'Response'
                ]
            );
        }

        $installer->endSetup();
    }

    public function _upgrade104(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()->changeColumn(
            $installer->getTable("hiperdino_raffle_rgpd"),
            "phone",
            "phone",
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Phone'
            ]
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable("hiperdino_raffle_rgpd"),
            "island",
            "island",
            [
                'type' => Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Island'
            ]
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable("hiperdino_raffle_rgpd"),
            "accept_rgpd",
            "accept_rgpd",
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Accept RGPD'
            ]
        );

        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                "hiperdino_raffle_rgpd",
                "island",
                "singular_islands",
                "id"
            ),
            "hiperdino_raffle_rgpd",
            "island",
            "singular_islands",
            "id",
            Table::ACTION_SET_NULL
        );

        $installer->endSetup();
    }

    protected function _upgrade105(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        if ($installer->getConnection()->isTableExists('hiperdino_participation_queue')) {
            $table = $installer->getTable('hiperdino_participation_queue');

            if (!$installer->getConnection()->tableColumnExists($table, 'id_customer')) {
                $installer->getConnection()->addColumn(
                    $table,
                    'id_customer',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Customer ID'
                    ]
                );
            }
        }

        $installer->endSetup();
    }

}
