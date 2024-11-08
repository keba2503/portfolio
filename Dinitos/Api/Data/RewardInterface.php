<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RewardInterface extends CustomAttributesDataInterface
{
    const ID = 'id';
    const STORES = 'stores';
    const ACTIVE = 'active';
    const TYPE = 'type';
    const ENTITY_IDENTIFIER = 'entity_identifier';
    const NAME = 'name';
    const DINITOS_QTY = 'dinitos_qty';
    const CHECKOUT_TEXT = 'checkout_text';
    const CART_TEXT = 'cart_text';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return ?string
     *
     */
    public function getId(): ?string;

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): static;

    /**
     * @return string[];
     */
    public function getStores(): array|string;

    /**
     * @param string $stores
     * @return $this
     */
    public function setStores(string $stores): static;

    /**
     * @return string;
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static;

    /**
     * @return string;
     */
    public function getActive(): string;

    /**
     * @param string $active
     * @return $this
     */
    public function setActive(string $active): static;

    /**
     * @return string;
     */
    public function getDinitosQty(): string;

    /**
     * @param string $dinitosQty
     * @return $this
     */
    public function setDinitosQty(string $dinitosQty): static;

    /**
     * @return string;
     */
    public function getCreatedAt(): string;

    /**
     * @param $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt): static;

    /**
     * @return string
     */
    public function getEntityIdentifier(): string;

    /**
     * @param string $entityIdentifier
     * @return $this
     */
    public function setEntityIdentifier(string $entityIdentifier): static;

    /**
     * @return ?string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static;

    /**
     * @return string
     */
    public function getCheckoutText(): string;

    /**
     * @param string $checkoutText
     * @return $this
     */
    public function setCheckoutText(string $checkoutText): static;

    /**
     * @return string
     */
    public function getCartText(): string;

    /**
     * @param string $cartText
     * @return $this
     */
    public function setCartText(string $cartText): static;

    /**
     * @return string;
     */
    public function getUpdatedAt(): string;

    /**
     * @param $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt): static;
}