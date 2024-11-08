<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\PackageInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Package extends AbstractModel implements IdentityInterface, PackageInterface
{
    const CACHE_TAG = 'hiperdino_dinitos_packages';
    protected $_cacheTag = 'hiperdino_dinitos_packages';
    protected $_eventPrefix = 'hiperdino_dinitos_packages';

    protected function _construct(): void
    {
        $this->_init('Hiperdino\Dinitos\Model\ResourceModel\Package');
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->getData(self::ID);
    }

    public function setId($id): static
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId(): ?string
    {
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId($incrementId): static
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): ?int
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId): static
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getDinitosQuantity(): ?int
    {
        return $this->_getData(self::DINITOS_QUANTITY);
    }

    /**
     * @inheritDoc
     */
    public function setDinitosQuantity($dinitosQuantity): static
    {
        return $this->setData(self::DINITOS_QUANTITY, $dinitosQuantity);
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDinitos(): ?int
    {
        return $this->_getData(self::AVAILABLE_DINITOS);
    }

    /**
     * @inheritDoc
     */
    public function setAvailableDinitos($availableDinitos): static
    {
        return $this->setData(self::AVAILABLE_DINITOS, $availableDinitos);
    }

    /**
     * @inheritDoc
     */
    public function getExpirationDate(): ?string
    {
        return $this->_getData(self::EXPIRATION_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setExpirationDate($expirationDate): static
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * @inheritDoc
     */
    public function getRedeemed(): ?bool
    {
        return $this->_getData(self::REDEEMED);
    }

    /**
     * @inheritDoc
     */
    public function setRedeemed($redeemed): static
    {
        return $this->setData(self::REDEEMED, $redeemed);
    }

    /**
     * @inheritDoc
     */
    public function getExpired(): ?bool
    {
        return $this->_getData(self::EXPIRED);
    }

    /**
     * @inheritDoc
     */
    public function setExpired($expired): static
    {
        return $this->setData(self::EXPIRED, $expired);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getCreatedAt(): ?string
    {
        return $this->_getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}