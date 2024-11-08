<?php

namespace Hiperdino\BlackFriday\Setup;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    const ATTRIBUTE_CODE = "product_tags";
    const NEW_OPTION_LABEL = "Black Friday";

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;
    /**
     * @var \Singular\Eav\Helper\Data
     */
    private $eavHelper;


    /**
     * InstallData constructor.
     * @param \Singular\Eav\Helper\Data $eavHelper
     */
    public function __construct(
        \Singular\Eav\Helper\Data $eavHelper
    )
    {
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