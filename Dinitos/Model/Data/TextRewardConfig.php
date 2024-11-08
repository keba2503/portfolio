<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\TextRewardConfigInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class TextRewardConfig extends AbstractExtensibleObject implements TextRewardConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getMainTitle()
    {
        return $this->_get(self::MAIN_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setMainTitle($mainTitle)
    {
        return $this->setData(self::MAIN_TITLE, $mainTitle);
    }

    /**
     * @inheritdoc
     */
    public function getTextToExpire()
    {
        return $this->_get(self::TEXT_TO_EXPIRE);
    }

    /**
     * @inheritdoc
     */
    public function setTextToExpire($textToExpire)
    {
        return $this->setData(self::TEXT_TO_EXPIRE, $textToExpire);
    }

    /**
     * @inheritdoc
     */
    public function getTextUrl()
    {
        return $this->_get(self::TEXT_URL);
    }

    /**
     * @inheritdoc
     */
    public function setTextUrl($textUrl)
    {
        return $this->setData(self::TEXT_URL, $textUrl);
    }

    /**
     * @inheritdoc
     */
    public function getCmsBlockId()
    {
        return $this->_get(self::CMS_BLOCK_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCmsBlockId($cmsBlockId)
    {
        return $this->setData(self::CMS_BLOCK_ID, $cmsBlockId);
    }

    /**
     * @inheritdoc
     */
    public function getMissingDigitsText()
    {
        return $this->_get(self::MISSING_DIGITS_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setMissingDigitsText($missingDigitsText)
    {
        return $this->setData(self::MISSING_DIGITS_TEXT, $missingDigitsText);
    }

    /**
     * @inheritdoc
     */
    public function getRewardValueText()
    {
        return $this->_get(self::REWARD_VALUE_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setRewardValueText($rewardValueText)
    {
        return $this->setData(self::REWARD_VALUE_TEXT, $rewardValueText);
    }

    /**
     * @inheritDoc
     */
    public function getDinitosLeftText()
    {
        return $this->_get(self::DINITOS_LEFT_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setDinitosLeftText($dinitosLeftText)
    {
        return $this->setData(self::DINITOS_LEFT_TEXT, $dinitosLeftText);
    }

    /**
     * @inheritDoc
     */
    public function getDinitosUseText()
    {
        return $this->_get(self::DINITOS_USE_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setDinitosUseText($dinitosUseText)
    {
        return $this->setData(self::DINITOS_USE_TEXT, $dinitosUseText);
    }

    /**
     * @inheritDoc
     */
    public function getDinitosAchieveText()
    {
        return $this->_get(self::DINITOS_ACHIEVE_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setDinitosAchieveText($dinitosAchieveText)
    {
        return $this->setData(self::DINITOS_ACHIEVE_TEXT, $dinitosAchieveText);
    }

    /**
     * @inheritDoc
     */
    public function getExcludeSelectedRewards()
    {
        return $this->_get(self::EXCLUDE_SELECTED_REWARDS);
    }

    /**
     * @inheritDoc
     */
    public function setExcludeSelectedRewards($excludeSelectedRewards)
    {
        return $this->setData(self::EXCLUDE_SELECTED_REWARDS, $excludeSelectedRewards);
    }

    /**
     * @inheritDoc
     */
    public function getBagsRewardText()
    {
        return $this->_get(self::BAGS_REWARD_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setBagsRewardText($bagsRewardText)
    {
        return $this->setData(self::BAGS_REWARD_TEXT, $bagsRewardText);
    }
}
