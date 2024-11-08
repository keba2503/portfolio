<?php

namespace Hiperdino\Anniversary2020\Model;

use Hiperdino\Anniversary2020\Api\Data\RaffleRgpdInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class RaffleRgpd extends AbstractModel implements IdentityInterface, RaffleRgpdInterface
{
    const CACHE_TAG = 'hiperdino_rafflergpd';

    protected $_cacheTag = 'hiperdino_rafflergpd';
    protected $_eventPrefix = 'hiperdino_rafflergpd';

    protected function _construct()
    {
        $this->_init('Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd');
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
        $keys = ['id', 'taxvat', 'island', 'customer_id', 'created_at', 'accept_rgpd'];

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
    public function getTaxvat()
    {
        return $this->getData(self::TAXVAT);
    }

    /**
     * @param string $taxvat
     * @return $this
     */
    public function setTaxvat($taxvat)
    {
        return $this->setData(self::TAXVAT, $taxvat);
    }

    /**
     * @return integer
     */
    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * @return string
     */
    public function getIsland()
    {
        return $this->getData(self::ISLAND);
    }

    /**
     * @param string $island
     * @return $this
     */
    public function setIsland($island)
    {
        return $this->setData(self::ISLAND, $island);
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
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getAcceptRgpd()
    {
        return $this->getData(self::ACCEPT_RGPD);
    }

    /**
     * @param string $acceptRgpd
     * @return $this
     */
    public function setAcceptRgpd($acceptRgpd)
    {
        return $this->setData(self::ACCEPT_RGPD, $acceptRgpd);
    }

    public function getCustomAttribute($attributeCode)
    {
        // TODO: Implement getCustomAttribute() method.
    }

    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: Implement setCustomAttribute() method.
    }

    public function getCustomAttributes()
    {
        // TODO: Implement getCustomAttributes() method.
    }

    public function setCustomAttributes(array $attributes)
    {
        // TODO: Implement setCustomAttributes() method.
    }
}