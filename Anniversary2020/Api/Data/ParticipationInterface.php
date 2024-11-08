<?php

namespace Hiperdino\Anniversary2020\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface ParticipationInterface extends CustomAttributesDataInterface
{
    const RAFFLE_ID = 'raffle_id';
    const PARTICIPATION_ID = 'participation_id';
    const PARTICIPATION_DATE = 'participation_date';
    const PRIZE = 'prize';
    const SALE_ID = 'sale_id';
    const COUPON_CODE = 'coupon_code';
    const HAS_PRIZE = 'has_prize';
    const WEEK_TITLE = 'week_title';
    const PRIZE_IMAGE_URL = 'prize_image_url';
    const REDEEMED_PRIZE = 'redeemed_prize';
    const CUSTOMER_ID = 'customer_id';
    const SCRATCH = 'scratch';
    const ASSOCIATED_RAFFLE = 'associated_raffle';
    const STORE = 'store';
    const SCRATCH_DATE = 'scratch_date';
    const RAFFLE_DATE = 'raffle_date';

    /**
     * @return int|null
     */
    public function getRaffleId();

    /**
     * @param int|null $raffleId
     * @return $this
     */
    public function setRaffleId($raffleId);

    /**
     * @return string|null
     */
    public function getParticipationId();

    /**
     * @param string|null $participationId
     * @return $this
     */
    public function setParticipationId($participationId);

    /**
     * @return string|null
     */
    public function getParticipationDate();

    /**
     * @param string|null $participationDate
     * @return $this
     */
    public function setParticipationDate($participationDate);

    /**
     * @return string|null
     */
    public function getPrize();

    /**
     * @param string|null $prize
     * @return $this
     */
    public function setPrize($prize);

    /**
     * @return int|null
     */
    public function getSaleId();

    /**
     * @param int|null $saleId
     * @return $this
     */
    public function setSaleId($saleId);

    /**
     * @return string|null
     */
    public function getCouponCode();

    /**
     * @param string|null $couponCode
     * @return $this
     */
    public function setCouponCode($couponCode);

    /**
     * @return string|null
     */
    public function getCustomerId();

    /**
     * @param string|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return bool|null
     */
    public function getScratch();

    /**
     * @param bool|null $scratch
     * * @return $this
     */
    public function setScratch($scratch);

    /**
     * @return bool|null
     */
    public function getAssociatedRaffle();

    /**
     * @param bool|null $associatedRaffle
     * @return $this
     */
    public function setAssociatedRaffle($associatedRaffle);

    /**
     * @return string|null
     */
    public function getStore();

    /**
     * @param string|null $store
     * @return $this
     */
    public function setStore($store);

    /**
     * @return string|null
     */
    public function getScratchDate();

    /**
     * @param string|null $scratchDate
     * @return $this
     */
    public function setScratchDate($scratchDate);

    /**
     * @return string|null
     */
    public function getRaffleDate();

    /**
     * @param string|null $raffleDate
     * @return $this
     */
    public function setRaffleDate($raffleDate);

    /**
     * @return bool|null
     */
    public function getHasPrize();

    /**
     * @param bool|null $hasPrize
     * @return $this
     */
    public function setHasPrize($hasPrize);

    /**
     * @return string|null
     */
    public function getWeekTitle();

    /**
     * @param string|null  $weekTitle
     * @return $this
     */
    public function setWeekTitle($weekTitle);

    /**
     * @return string|null
     */
    public function getPrizeImageUrl();

    /**
     * @param string|null $prizeImageUrl
     * @return $this
     */
    public function setPrizeImageUrl($prizeImageUrl);

    /**
     * @return bool|null
     */
    public function getRedeemedPrize();

    /**
     * @param bool|null $redeemedPrize
     * @return $this
     */
    public function setRedeemedPrize($redeemedPrize);
}