<?php

namespace Hiperdino\BlackFriday\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $_eavSetupFactory;
    protected $_eavConfig;
    protected $_storeManager;
    protected $_attributeFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_eavConfig = $eavConfig;
        $this->_storeManager = $storeManager;
        $this->_attributeFactory = $attributeFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->_upgrade_1_0_2($setup);
        }
    }

    /**
     * actualizacion 1.0.2
     *
     * @param ModuleDataSetupInterface $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _upgrade_1_0_2(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();
        $entityTypeId = \Magento\Catalog\Model\Product::ENTITY;
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Hiperdino');

        // blackfriday_max_buy_qty
        $eavSetup->addAttribute(
            $entityTypeId,
            'blackfriday_max_buy_qty',
            [
                'type' => 'text',
                'label' => 'Máxima cantidad de compra BlackFriday',
                'input' => 'text',
                'position' => 300,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false
            ]
        );
        $eavSetup->addAttributeToSet($entityTypeId, $attributeSetId, $groupId, 'blackfriday_max_buy_qty');

        $setup->endSetup();
    }
}
