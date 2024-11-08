<?php

namespace Hiperdino\Dinitos\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected AdapterInterface $connection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->_upgrade101();
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->_upgrade102();
        }
        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $this->upgrade103($setup);
        }

        $setup->endSetup();
    }

    protected function _upgrade101(): void
    {
        $this->connection->query("
			CREATE TABLE hiperdino_dinitos (
				id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT,
				product_sku VARCHAR (255) NOT NULL,
				store VARCHAR(15) NOT NULL,
				is_active SMALLINT(1) NOT NULL DEFAULT 0,
				dinitos SMALLINT(5) NOT NULL DEFAULT 0,
				gramos DECIMAL(10,3),
				from_date DATETIME,
				to_date DATETIME,
				PRIMARY KEY (id)
			)
		");
    }

    protected function _upgrade102(): void
    {
        $this->connection->query("
            ALTER TABLE hiperdino_dinitos
            ADD processed SMALLINT(1) NOT NULL DEFAULT 0
        ");
    }

    public function upgrade103($setup): void
    {
        $connection = $setup->getConnection();
        $salesRuleTable = $setup->getTable('salesrule');

        if (!$connection->tableColumnExists($salesRuleTable, 'discount_type')) {
            $connection->addColumn(
                $salesRuleTable,
                'discount_type',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Tipo de descuento'
                ]
            );
        }
    }
}
