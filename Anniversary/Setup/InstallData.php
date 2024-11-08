<?php

namespace Hiperdino\Anniversary\Setup;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Singular\Eav\Helper\Data;

class InstallData implements InstallDataInterface
{
    const ATTRIBUTE_CODE = "product_tags";
    const NEW_OPTION_LABEL = "Aniversario";

    protected ProductAttributeRepositoryInterface $attributeRepository;
    protected array $attributeValues;
    protected TableFactory $tableFactory;
    protected AttributeOptionManagementInterface $attributeOptionManagement;
    protected AttributeOptionLabelInterfaceFactory $optionLabelFactory;
    protected AttributeOptionInterfaceFactory $optionFactory;
    protected Data $eavHelper;

    /**
     * InstallData constructor.
     * @param Data $eavHelper
     */
    public function __construct(
        Data $eavHelper
    ) {
        $this->eavHelper = $eavHelper;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $attributeCode = self::ATTRIBUTE_CODE;
        $label = self::NEW_OPTION_LABEL;

        try {
            $this->eavHelper->getAttributeOption($attributeCode, $label);

        } catch (InputException $e) {
        } catch (NoSuchEntityException $e) {
        } catch (StateException $e) {
        }

        $setup->endSetup();
    }
}
