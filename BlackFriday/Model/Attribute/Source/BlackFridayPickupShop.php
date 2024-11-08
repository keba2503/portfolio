<?php

namespace Hiperdino\BlackFriday\Model\Attribute\Source;

class BlackFridayPickupShop extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_tiendasRepository;
    protected $_searchCriteriaBuilder;

    public function __construct(
        \Singular\Tiendas\Model\TiendasRepository $tiendasRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_tiendasRepository = $tiendasRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [['value' => "", 'label' => " "]];
            $tiendas = $this->_tiendasRepository->getList(
                $this->_searchCriteriaBuilder->addFilter('is_black_friday', 1)->create()
            );
            foreach ($tiendas->getItems() as $tienda) {
                $this->_options[] = [
                    'value' => $tienda->getId(),
                    'label' => $tienda->getName()
                ];
            }
        }

        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
}
