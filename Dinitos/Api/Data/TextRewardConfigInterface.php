<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface TextRewardConfigInterface extends CustomAttributesDataInterface
{
    const MAIN_TITLE = 'main_title';
    const TEXT_TO_EXPIRE = 'text_to_expire';
    const TEXT_URL = 'text_url';
    const CMS_BLOCK_ID = 'cms_block_id';
    const MISSING_DIGITS_TEXT = 'missing_digits_text';
    const REWARD_VALUE_TEXT = 'reward_value_text';
    const DINITOS_LEFT_TEXT = 'dinitos_left_text';
    const DINITOS_USE_TEXT = 'dinitos_use_text';
    const DINITOS_ACHIEVE_TEXT = 'dinitos_achieve_text';
    const EXCLUDE_SELECTED_REWARDS = 'exclude_selected_rewards';
    const BAGS_REWARD_TEXT = 'bags_reward_text';
    /**#@-*/

    /**
     * @return string
     */
    public function getMainTitle();

    /**
     * @param string $mainTitle
     * @return $this
     */
    public function setMainTitle($mainTitle);

    /**
     * @return string
     */
    public function getTextToExpire();

    /**
     * @param string $textToExpire
     * @return $this
     */
    public function setTextToExpire($textToExpire);

    /**
     * @return string
     */
    public function getTextUrl();

    /**
     * @param string $textUrl
     * @return $this
     */
    public function setTextUrl($textUrl);

    /**
     * @return string
     */
    public function getCmsBlockId();

    /**
     * @param string $cmsBlockId
     * @return $this
     */
    public function setCmsBlockId($cmsBlockId);

    /**
     * @return string
     */
    public function getMissingDigitsText();

    /**
     * @param string $missingDigitsText
     * @return $this
     */
    public function setMissingDigitsText($missingDigitsText);

    /**
     * @return string
     */
    public function getRewardValueText();

    /**
     * @param string $rewardValueText
     * @return $this
     */
    public function setRewardValueText($rewardValueText);

    /**
     * @return string
     */
    public function getDinitosLeftText();

    /**
     * @param $dinitosLeftText
     * @return $this
     */
    public function setDinitosLeftText($dinitosLeftText);

    /**
     * @return string
     */
    public function getDinitosUseText();

    /**
     * @param $dinitosUseText
     * @return $this
     */
    public function setDinitosUseText($dinitosUseText);

    /**
     * @return string
     */
    public function getDinitosAchieveText();

    /**
     * @param $dinitosAchieveText
     * @return $this
     */
    public function setDinitosAchieveText($dinitosAchieveText);

    /**
     * @return bool
     */
    public function getExcludeSelectedRewards();

    /**
     * @param $excludeSelectedRewards
     * @return $this
     */
    public function setExcludeSelectedRewards($excludeSelectedRewards);

    /**
     * @return string
     */
    public function getBagsRewardText();

    /**
     * @param $bagsRewardText
     * @return $this
     */
    public function setBagsRewardText($bagsRewardText);
}
