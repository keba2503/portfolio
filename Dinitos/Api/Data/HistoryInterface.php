<?php

namespace Hiperdino\Dinitos\Api\Data;

interface HistoryInterface
{
    const ID = 'id';
    const CONCEPT = 'concept';
    const INCREMENT_ID = 'increment_id';
    const CUSTOMER_ID = 'customer_id';
    const DINITOS_QUANTITY = 'dinitos_quantity';
    const TRANSACTION_TYPE = 'transaction_type';
    const PACKAGE_ID = 'package_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DINITOS_BALANCE = 'dinitos_balance';
    const EXPIRATION_DATE = 'expiration_date';
    const PACKAGE_HISTORY = 'package_history';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getConcept();

    /**
     * @param string $concept
     * @return $this
     */
    public function setConcept($concept);

    /**
     * @return string|null
     */
    public function getIncrementId();

    /**
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getDinitosQuantity();

    /**
     * @param int $dinitosQuantity
     * @return $this
     */
    public function setDinitosQuantity($dinitosQuantity);

    /**
     * @return string
     */
    public function getTransactionType();

    /**
     * @param string $transactionType
     * @return $this
     */
    public function setTransactionType($transactionType);

    /**
     * @return int
     */
    public function getPackageId();

    /**
     * @param int $packageId
     * @return $this
     */
    public function setPackageId($packageId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getDinitosBalance();

    /**
     * @param int $dinitosBalance
     * @return $this
     */
    public function setDinitosBalance($dinitosBalance);

    /**
     * @return ?string
     */
    public function getExpirationDate();

    /**
     * @param ?string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate);

    /**
     * @return ?string
     */
    public function getPackageHistory();

    /**
     * @param ?string $historyPackage
     * @return $this
     */
    public function setPackageHistory($historyPackage);
}
