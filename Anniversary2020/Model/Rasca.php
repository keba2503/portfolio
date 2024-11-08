<?php

namespace Hiperdino\Anniversary2020\Model;

use Hiperdino\Anniversary2020\Api\Data\RascaInterface;
use LogicException;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Rasca extends AbstractModel implements IdentityInterface, RascaInterface
{
    const CACHE_TAG = 'hiperdino_anniversary2020_rasca';
    const ICON_DIR = 'rascas';
    const TYPE_ONLINE = 1;
    const TYPE_OFFLINE = 2;

    protected $_cacheTag = 'hiperdino_anniversary2020_rasca';
    protected $_eventPrefix = 'hiperdino_anniversary2020_rasca';

    protected function _construct()
    {
        $this->_init('Hiperdino\Anniversary2020\Model\ResourceModel\Rasca');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }

    public function toArray(array $keys = [])
    {
        $keys = ['id', 'rasca_code', 'customer_id', 'date', 'week_id'];

        return parent::toArray($keys);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getRascaCode()
    {
        return $this->getData(self::RASCA_CODE);
    }

    /**
     * @param string $rascaCode
     * @return $this
     */
    public function setRascaCode($rascaCode)
    {
        return $this->setData(self::RASCA_CODE, $rascaCode);
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * @return string
     */
    public function getWeekId()
    {
        return $this->getData(self::WEEK_ID);
    }

    /**
     * @param string $weekId
     * @return $this
     */
    public function setWeekId($weekId)
    {
        return $this->setData(self::WEEK_ID, $weekId);
    }

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return AttributeInterface|null
     */
    public function getCustomAttribute($attributeCode)
    {
        // TODO: Implement getCustomAttribute() method.
    }

    /**
     * Set an attribute value for a given attribute code
     *
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: Implement setCustomAttribute() method.
    }

    /**
     * Retrieve custom attributes values.
     *
     * @return AttributeInterface[]|null
     */
    public function getCustomAttributes()
    {
        // TODO: Implement getCustomAttributes() method.
    }

    /**
     * Set array of custom attributes
     *
     * @param AttributeInterface[] $attributes
     * @return $this
     * @throws LogicException
     */
    public function setCustomAttributes(array $attributes)
    {
        // TODO: Implement setCustomAttributes() method.
    }
}
