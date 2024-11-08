<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\HistoryListInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Model\AbstractModel;

class HistoryList extends AbstractModel implements HistoryListInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
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
     * @param $attributeCode
     * @return void
     */
    public function getCustomAttribute($attributeCode)
    {
        // TODO: implement getCustomAttribute() method.
    }

    /**
     * @param $attributeCode
     * @param $attributeValue
     * @return
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: implement setCustomAttribute() method.
    }

    /**
     * @return
     */
    public function getCustomAttributes()
    {
        // TODO: implement getCustomAttributes() method.
    }

    /**
     * @param AttributeInterface|null $attributes
     * @return HistoryList
     */
    public function setCustomAttributes($attributes)
    {
        // TODO: implement setCustomAttributes() method.
    }
}