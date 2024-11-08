<?php

namespace Hiperdino\Dinitos\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    protected EavSetupFactory $eavSetupFactory;
    protected Config $eavConfig;
    protected CustomerSetupFactory $customerSetupFactory;
    protected SalesSetupFactory $salesSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        CustomerSetupFactory $customerSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        if (version_compare($context->getVersion(), '1.0.3') < 0) $this->upgrade103($setup);
        if (version_compare($context->getVersion(), '1.0.4') < 0) $this->upgrade104($setup);
        if (version_compare($context->getVersion(), '1.0.5') < 0) $this->upgrade105($setup);
    }

    protected function upgrade103(ModuleDataSetupInterface $setup): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = Customer::ENTITY;
        $setup->startSetup();

        $eavSetup->addAttribute($entityTypeId, 'dinitos', [
            'type' => 'int',
            'label' => 'Dinitos',
            'input' => 'text',
            'required' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => 2010,
            'position' => 2010,
            'system' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'visible' => true,
            'adminhtml_only' => false
        ]);
        $attribute = $this->eavConfig->getAttribute($entityTypeId, 'dinitos');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();

        $setup->endSetup();
    }

    protected function upgrade104(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        $salesSetup->addAttribute(
            Order::ENTITY,
            'dinitos_rewards',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 16777217,
                'label' => 'Dinitos - Recompensas',
                'user_defined' => true,
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => 1,
                'system' => 1,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

        $setup->endSetup();
    }

    protected function upgrade105(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'dinitos_rewards_discount_split',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Dinitos Rewards Discount Split. It represent the rewards that has been applied'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'dinitos_rewards_base_discount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,4',
                'comment' => 'Dinitos Rewards base discount. It only counts rewards of the product type'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'dinitos_rewards_discount_amount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,4',
                'comment' => 'Dinitos Rewards discount amount. It only counts rewards of the product type'
            ]
        );

        $salesSetup->addAttribute(
            \Magento\Sales\Model\Order::ENTITY,
            'dinitos_rewards_discount_split',
            [
                'type' => 'text',
                'label' => 'Dinitos Rewards Discount Split',
                'input' => 'text',
                'user_defined' => true,
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => 1,
                'system' => 1,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'dinitos_rewards_discount_split',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Dinitos Rewards Discount Split. It represent the rewards that has been applied'
            ]
        );

        $salesSetup->addAttribute(
            Order::ENTITY,
            'dinitos_rewards_base_discount',
            [
                'type' => 'decimal',
                'label' => 'Dinitos Rewards base discount amount',
                'input' => 'text',
                'user_defined' => true,
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => 1,
                'system' => 1,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'dinitos_rewards_base_discount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,4',
                'comment' => 'Dinitos Rewards base amount. It only counts rewards of the product type'
            ]
        );

        $salesSetup->addAttribute(
            Order::ENTITY,
            'dinitos_rewards_discount_amount',
            [
                'type' => 'decimal',
                'label' => 'Dinitos Rewards discount amount',
                'input' => 'text',
                'user_defined' => true,
                'backend' => '',
                'frontend' => '',
                'source' => '',
                'global' => 1,
                'system' => 1,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => false,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'dinitos_rewards_discount_amount',
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,4',
                'comment' => 'Dinitos Rewards discount amount. It only counts rewards of the product type'
            ]
        );

        $setup->endSetup();
    }
}
