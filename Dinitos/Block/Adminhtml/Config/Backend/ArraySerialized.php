<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class ArraySerialized extends ConfigValue
{
    protected SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $configEnable = $this->getFieldsetDataValue('original_filters_enabled');

        if (is_array($value)) {
            if (count(array_diff_key($value, ['__empty' => true])) === 0 && !$configEnable) {
                if (empty($value)) {
                    $this->setValue(null);
                    return;
                }
            }

            unset($value['__empty']);
        }

        $encodedValue = $this->serializer->serialize($value);
        $this->setValue($encodedValue);
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        if ($value) {
            $decodedValue = $this->serializer->unserialize($value);
            $this->setValue($decodedValue);
        }
    }
}