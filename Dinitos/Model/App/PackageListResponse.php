<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\PackageListResponseInterface;
use Magento\Framework\Model\AbstractModel;

class PackageListResponse extends AbstractModel implements PackageListResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getPackages()
    {
        return $this->_getData(self::PACKAGES);
    }

    /**
     * @inheritDoc
     */
    public function setPackages($packages)
    {
        return $this->setData(self::PACKAGES, $packages);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttribute($attributeCode)
    {
        // TODO: Implement getCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: Implement setCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttributes()
    {
        // TODO: Implement getCustomAttributes() method.
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttributes(array $attributes)
    {
        // TODO: Implement setCustomAttributes() method.
    }

}