<?php

namespace Hiperdino\Dinitos\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class ExpirationSendInterval extends Value
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected Context $context,
        protected Registry $registry,
        protected ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        /** @var array $value */
        $value = $this->getValue();
        unset($value['__empty']);
        $encodedValue = json_encode($value);
        $this->setValue($encodedValue);
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (isset($value)) {
            $decodedValue = json_decode($value, associative: true);
            $this->setValue($decodedValue);
        }
    }
}