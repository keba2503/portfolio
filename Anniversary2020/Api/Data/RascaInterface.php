<?php

namespace Hiperdino\Anniversary2020\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Rasca interface.
 * @api
 */
interface RascaInterface extends CustomAttributesDataInterface
{
    const ID = 'id';
    const RASCA_CODE = 'rasca_code';
    const CUSTOMER_ID = 'customer_id';
    const DATE = 'date';
    const WEEK_ID = 'week_id';

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
    public function getRascaCode();

    /**
     * @param string $rascaCode
     * @return $this
     */
    public function setRascaCode($rascaCode);

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
    public function getDate();

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date);

    /**
     * @return string
     */
    public function getWeekId();

    /**
     * @param string $weekId
     * @return $this
     */
    public function setWeekId($weekId);
}
