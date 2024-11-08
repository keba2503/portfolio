<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\RewardInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Reward extends AbstractModel implements RewardInterface, IdentityInterface
{
    const CACHE_TAG = 'hiperdino_dinitos_reward';
    protected $_cacheTag = 'hiperdino_dinitos_reward';
    protected $_eventPrefix = 'hiperdino_dinitos_reward';

    public function _construct(): void
    {
        $this->_init('Hiperdino\Dinitos\Model\ResourceModel\Reward');
    }

    public function getId(): ?string
    {
        return $this->getData(self::ID);
    }

    public function setId($id): static
    {
        return $this->setData(self::ID, $id);
    }

    public function getStores(): array|string
    {
        return $this->getData(self::STORES);
    }

    public function setStores($stores): static
    {
        return $this->setData(self::STORES, $stores);
    }

    public function getType(): string
    {
        return $this->getData(self::TYPE);
    }

    public function setType($type): static
    {
        return $this->setData(self::TYPE, $type);
    }

    public function getActive(): string
    {
        return $this->getData(self::ACTIVE);
    }

    public function setActive($active): static
    {
        return $this->setData(self::ACTIVE, $active);
    }

    public function getDinitosQty(): string
    {
        return $this->getData(self::DINITOS_QTY);
    }

    public function setDinitosQty($dinitosQty): static
    {
        return $this->setData(self::DINITOS_QTY, $dinitosQty);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt): static
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getEntityIdentifier(): string
    {
        return $this->getData(self::ENTITY_IDENTIFIER);
    }

    public function setEntityIdentifier(string $entityIdentifier): static
    {
        return $this->setData(self::ENTITY_IDENTIFIER, $entityIdentifier);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): static
    {
        return $this->setData(self::NAME, $name);
    }

    public function getCheckoutText(): string
    {
        return $this->getData(self::CHECKOUT_TEXT);
    }

    public function setCheckoutText(string $checkoutText): static
    {
        return $this->setData(self::CHECKOUT_TEXT, $checkoutText);
    }

    public function getCartText(): string
    {
        return $this->getData(self::CART_TEXT);
    }

    public function setCartText(string $cartText): static
    {
        return $this->setData(self::CART_TEXT, $cartText);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt): static
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
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