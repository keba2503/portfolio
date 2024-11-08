<?php

namespace Hiperdino\Anniversary2020\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * RaffleRgpd interface.
 * @api
 */
interface RaffleRgpdInterface extends CustomAttributesDataInterface
{
    const ID = 'id';
    const TAXVAT = 'taxvat';
    const ISLAND = 'island';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
    const PHONE = 'phone';
    const ACCEPT_RGPD = 'accept_rgpd';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTaxvat();

    /**
     * @param string $taxvat
     * @return $this
     */
    public function setTaxvat($taxvat);

    /**
     * @return integer
     */
    public function getPhone();

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getIsland();

    /**
     * @param string $island
     * @return $this
     */
    public function setIsland($island);

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

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
    public function getAcceptRgpd();

    /**
     * @param string $acceptRgpd
     * @return $this
     */
    public function setAcceptRgpd($acceptRgpd);
}