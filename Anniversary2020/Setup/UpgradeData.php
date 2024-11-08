<?php

namespace Hiperdino\Anniversary2020\Setup;

use Hiperdino\Customer\Model\Customer\Attribute\Source\DefaultCustomerIsland;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $eavSetupFactory;
    protected $eavConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.2') < 0) $this->_upgrade002($setup);
        if (version_compare($context->getVersion(), '0.0.3') < 0) $this->_upgrade003($setup);
        if (version_compare($context->getVersion(), '0.0.4') < 0) $this->_upgrade004($setup);
        if (version_compare($context->getVersion(), '0.0.5') < 0) $this->_upgrade005($setup);
    }

    /**
     * Actualización 0.0.2 Añadimos atributos de customer_islant y el customer_telephone
     *
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function _upgrade002(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = Customer::ENTITY;
        $setup->startSetup();

        // customer_island
        $eavSetup->addAttribute($entityTypeId, 'customer_island', [
            'type' => 'int',
            'label' => 'Isla seleccionada por el cliente',
            'input' => 'select',
            'source' => DefaultCustomerIsland::class,
            'required' => false,
            'default' => '',
            'sort_order' => 900,
            'position' => 900,
            'system' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'visible' => true,
            'adminhtml_only' => true
        ]);

        $attribute = $this->eavConfig->getAttribute($entityTypeId, 'customer_island');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();

        // customer_telephone
        $eavSetup->addAttribute($entityTypeId, 'customer_telephone', [
            'type' => 'varchar',
            'label' => 'Teléfono Aniversario',
            'input' => 'text',
            'required' => false,
            'default' => '',
            'sort_order' => 2040,
            'position' => 2040,
            'system' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'visible' => true,
            'adminhtml_only' => false
        ]);

        $attribute = $this->eavConfig->getAttribute($entityTypeId, 'customer_telephone');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();

        $setup->endSetup();
    }

    /**
     * Actualización 0.0.3
     * Añadimos columna week_id a la tabla de rascas
     *
     * @param ModuleDataSetupInterface $setup
     */
    protected function _upgrade003(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('hiperdino_anniversary2020_rascas');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $connection = $setup->getConnection();
            $connection->addColumn($tableName, 'week_id', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'ID de la semana'
            ]);
        }
        $setup->endSetup();
    }

    /**
     * Actualización 0.0.4
     * Añadimos el check aniversario 2021
     *
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function _upgrade004(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = Customer::ENTITY;
        $setup->startSetup();

        $eavSetup->addAttribute($entityTypeId, 'accept_anniversary_2021', [
            'type' => 'int',
            'label' => 'Acepta Aniversario 2021',
            'input' => 'select',
            'source' => Boolean::class,
            'required' => false,
            'default' => '0',
            'unique' => false,
            'sort_order' => 2030,
            'position' => 2030,
            'system' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'visible' => true,
            'adminhtml_only' => false
        ]);

        $attribute = $this->eavConfig->getAttribute($entityTypeId, 'accept_anniversary_2021');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();
    }

    /**
     * Actualización 0.0.5
     * Añadimos el check aniversario 2022
     *
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function _upgrade005(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = Customer::ENTITY;
        $setup->startSetup();

        $eavSetup->addAttribute($entityTypeId, 'accept_anniversary_2022', [
            'type' => 'int',
            'label' => 'Acepta Aniversario 2022',
            'input' => 'select',
            'source' => Boolean::class,
            'required' => false,
            'default' => '0',
            'unique' => false,
            'sort_order' => 2040,
            'position' => 2040,
            'system' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'visible' => true,
            'adminhtml_only' => false
        ]);

        $attribute = $this->eavConfig->getAttribute($entityTypeId, 'accept_anniversary_2022');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $attribute->save();
    }

}
