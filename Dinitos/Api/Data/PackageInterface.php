<?php

namespace Hiperdino\Dinitos\Api\Data;

interface PackageInterface
{
    const ID = 'id';
    const INCREMENT_ID = 'increment_id';
    const CUSTOMER_ID = 'customer_id';
    const DINITOS_QUANTITY = 'dinitos_quantity';
    const AVAILABLE_DINITOS = 'available_dinitos';
    const EXPIRATION_DATE = 'expiration_date';
    const EXPIRED = 'expired';
    const REDEEMED = 'redeemed';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id): static;

    /**
     * @return string|null
     */
    public function getIncrementId(): ?string;

    /**
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId): static;

    /**
     * @return int
     */
    public function getCustomerId(): ?int;

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId): static;

    /**
     * @return int
     */
    public function getDinitosQuantity(): ?int;

    /**
     * @param int $dinitosQuantity
     * @return $this
     */
    public function setDinitosQuantity($dinitosQuantity): static;

    /**
     * @return int
     */
    public function getAvailableDinitos(): ?int;

    /**
     * @param int $availableDinitos
     * @return $this
     */
    public function setAvailableDinitos($availableDinitos): static;

    /**
     * @return string
     */
    public function getExpirationDate(): ?string;

    /**
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate): static;

    /**
     * @return bool
     */
    public function getRedeemed(): ?bool;

    /**
     * @param bool $redeemed
     * @return $this
     */
    public function setRedeemed($redeemed): static;

    /**
     * @return bool
     */
    public function getExpired(): ?bool;

    /**
     * @param bool $expired
     * @return $this
     */
    public function setExpired($expired): static;

    /**
     * @return string
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt(): ?string;
}