<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\HistoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel implements IdentityInterface, HistoryInterface
{
    const CACHE_TAG = 'hiperdino_dinitos_history';
    protected $_cacheTag = 'hiperdino_dinitos_history';
    protected $_eventPrefix = 'hiperdino_dinitos_history';

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }

    protected function _construct()
    {
        $this->_init('Hiperdino\Dinitos\Model\ResourceModel\History');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getConcept()
    {
        return $this->_getData(self::CONCEPT);
    }

    /**
     * @param string $concept
     * @return $this
     */
    public function setConcept($concept)
    {
        return $this->setData(self::CONCEPT, $concept);
    }

    /**
     * @return string|null
     */
    public function getIncrementId()
    {
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
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
        return $this->_getData(self::CREATED_AT);
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
     * @return int
     */
    public function getDinitosQuantity()
    {
        return $this->_getData(self::DINITOS_QUANTITY);
    }

    /**
     * @param int $dinitosQuantity
     * @return $this
     */
    public function setDinitosQuantity($dinitosQuantity)
    {
        return $this->setData(self::DINITOS_QUANTITY, $dinitosQuantity);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->_getData(self::TRANSACTION_TYPE);
    }

    /**
     * @param string $transactionType
     * @return $this
     */
    public function setTransactionType($transactionType)
    {
        return $this->setData(self::TRANSACTION_TYPE, $transactionType);
    }

    /**
     * @return int
     */
    public function getPackageId()
    {
        return $this->_getData(self::PACKAGE_ID);
    }

    /**
     * @param int $packageId
     * @return $this
     */
    public function setPackageId($packageId)
    {
        return $this->setData(self::PACKAGE_ID, $packageId);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @param $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getDinitosBalance()
    {
        return $this->_getData(self::DINITOS_BALANCE);
    }

    public function setDinitosBalance($dinitosBalance)
    {
        return $this->setData(self::DINITOS_BALANCE, $dinitosBalance);
    }

    public function getExpirationDate()
    {
        return $this->_getData(self::EXPIRATION_DATE);
    }

    public function setExpirationDate($expirationDate)
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    public function getPackageHistory()
    {
        return $this->_getData(self::PACKAGE_HISTORY);
    }

    public function setPackageHistory($historyPackage)
    {
        return $this->setData(self::PACKAGE_HISTORY, $historyPackage);
    }
}
