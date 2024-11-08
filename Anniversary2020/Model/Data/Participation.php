<?php

namespace Hiperdino\Anniversary2020\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Hiperdino\Anniversary2020\Api\Data\ParticipationInterface;

/**
 * Class ParticipationByCustomerManagerInterface
 * @package Hiperdino\Anniversary2020\Model\Data
 */
class Participation extends AbstractExtensibleObject implements ParticipationInterface
{
    /**
     * Get the raffle ID.
     *
     * @return int|null
     */
    public function getRaffleId()
    {
        return $this->_get(self::RAFFLE_ID);
    }

    /**
     * Set the raffle ID.
     *
     * @param int|null $raffleId
     * @return $this
     */
    public function setRaffleId($raffleId)
    {
        return $this->setData(self::RAFFLE_ID, $raffleId);
    }

    /**
     * Get the participation ID.
     *
     * @return string|null
     */
    public function getParticipationId()
    {
        return $this->_get(self::PARTICIPATION_ID);
    }

    /**
     * Set the participation ID.
     *
     * @param string|null $participationId
     * @return $this
     */
    public function setParticipationId($participationId)
    {
        return $this->setData(self::PARTICIPATION_ID, $participationId);
    }

    /**
     * Get the participation date.
     *
     * @return string|null
     */
    public function getParticipationDate()
    {
        return $this->_get(self::PARTICIPATION_DATE);
    }

    /**
     * Set the participation date.
     *
     * @param string|null $participationDate
     * @return $this
     */
    public function setParticipationDate($participationDate)
    {
        return $this->setData(self::PARTICIPATION_DATE, $participationDate);
    }

    /**
     * Get the prize.
     *
     * @return string|null
     */
    public function getPrize()
    {
        return $this->_get(self::PRIZE);
    }

    /**
     * Set the prize.
     *
     * @param string|null $prize
     * @return $this
     */
    public function setPrize($prize)
    {
        return $this->setData(self::PRIZE, $prize);
    }

    /**
     * Get the sale ID.
     *
     * @return int|null
     */
    public function getSaleId()
    {
        return $this->_get(self::SALE_ID);
    }

    /**
     * Set the sale ID.
     *
     * @param int|null $saleId
     * @return $this
     */
    public function setSaleId($saleId)
    {
        return $this->setData(self::SALE_ID, $saleId);
    }

    /**
     * Get the coupon code.
     *
     * @return string|null
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }

    /**
     * Set the coupon.
     *
     * @param string|null $couponCode
     * @return $this
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }


    /**
     * Get the customer ID.
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set the customer ID.
     *
     * @param string|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get the scratch.
     *
     * @return bool|null
     */
    public function getScratch()
    {
        return $this->_get(self::SCRATCH);
    }

    /**
     * Set the scratch.
     *
     * @param bool|null $scratch
     * @return $this
     */
    public function setScratch($scratch)
    {
        return $this->setData(self::SCRATCH, $scratch);
    }

    /**
     * Get the associated raffle.
     *
     * @return bool|null
     */
    public function getAssociatedRaffle()
    {
        return $this->_get(self::ASSOCIATED_RAFFLE);
    }

    /**
     * Set the associated raffle.
     *
     * @param bool|null $associatedRaffle
     * @return $this
     */
    public function setAssociatedRaffle($associatedRaffle)
    {
        return $this->setData(self::ASSOCIATED_RAFFLE, $associatedRaffle);
    }

    /**
     * Get the store.
     *
     * @return string|null
     */
    public function getStore()
    {
        return $this->_get(self::STORE);
    }

    /**
     * Set the store.
     *
     * @param string|null $store
     * @return $this
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
    }

    /**
     * Get the scratch date.
     *
     * @return string|null
     */
    public function getScratchDate()
    {
        return $this->_get(self::SCRATCH_DATE);
    }

    /**
     * Set the scratch date.
     *
     * @param string|null $scratchDate
     * @return $this
     */
    public function setScratchDate($scratchDate)
    {
        return $this->setData(self::SCRATCH_DATE, $scratchDate);
    }

    /**
     * Get the raffle date.
     *
     * @return string|null
     */
    public function getRaffleDate()
    {
        return $this->_get(self::RAFFLE_DATE);
    }

    /**
     * Set the raffle date.
     *
     * @param string|null $raffleDate
     * @return $this
     */
    public function setRaffleDate($raffleDate)
    {
        return $this->setData(self::RAFFLE_DATE, $raffleDate);
    }

    /**
     * @return bool|null
     */
    public function getHasPrize()
    {
        return $this->_get(self::HAS_PRIZE);
    }

    /**
     * @param bool|null $hasPrize
     * @return $this
     */
    public function setHasPrize($hasPrize)
    {
        return $this->setData(self::HAS_PRIZE, $hasPrize);
    }

    /**
     * @return string|null
     */
    public function getWeekTitle()
    {
        return $this->_get(self::WEEK_TITLE);
    }

    /**
     * @param string|null $weekTitle
     * @return $this
     */
    public function setWeekTitle($weekTitle)
    {
        return $this->setData(self::WEEK_TITLE, $weekTitle);
    }

    /**
     * @return string|null
     */
    public function getPrizeImageUrl()
    {
        return $this->_get(self::PRIZE_IMAGE_URL);
    }

    /**
     * @param string|null $prizeImageUrl
     * @return $this
     */
    public function setPrizeImageUrl($prizeImageUrl)
    {
        return $this->setData(self::PRIZE_IMAGE_URL, $prizeImageUrl);
    }

    /**
     * @return bool|null
     */
    public function getRedeemedPrize()
    {
        return $this->_get(self::REDEEMED_PRIZE);
    }

    /**
     * @param bool|null $redeemedPrize
     * @return $this
     */
    public function setRedeemedPrize($redeemedPrize)
    {
        return $this->setData(self::REDEEMED_PRIZE, $redeemedPrize);
    }
}