<?php

namespace Hiperdino\Dinitos\Model\Data;

use Hiperdino\Dinitos\Api\Data\TextHistoryConfigInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class TextHistoryConfig extends AbstractExtensibleObject implements TextHistoryConfigInterface
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
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
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
    public function getTextUrlOnboarding()
    {
        return $this->_get(self::TEXT_URL_ONBOARDING);
    }

    /**
     * @inheritdoc
     */
    public function setTextUrlOnboarding($textUrlOnboarding)
    {
        return $this->setData(self::TEXT_URL_ONBOARDING, $textUrlOnboarding);
    }

    /**
     * @inheritdoc
     */
    public function getCmsBlockIdOnboarding()
    {
        return $this->_get(self::URL_ONBOARDING);
    }

    /**
     * @inheritdoc
     */
    public function setCmsBlockIdOnboarding($urlOnboarding)
    {
        return $this->setData(self::URL_ONBOARDING, $urlOnboarding);
    }

    /**
     * @inheritdoc
     */
    public function getTextLinkSidebar()
    {
        return $this->_get(self::TEXT_LINK_SIDEBAR);
    }

    /**
     * @inheritdoc
     */
    public function setTextLinkSidebar($textLinkSidebar)
    {
        return $this->setData(self::TEXT_LINK_SIDEBAR, $textLinkSidebar);
    }

    /**
     * @inheritdoc
     */
    public function getIconSidebar()
    {
        return $this->_get(self::ICON_SIDEBAR);
    }

    /**
     * @inheritdoc
     */
    public function setIconSidebar($iconSidebar)
    {
        return $this->setData(self::ICON_SIDEBAR, $iconSidebar);
    }

    /**
     * @inheritDoc
     */
    public function getEmptyHistoryText()
    {
        return $this->_get(self::EMPTY_HISTORY_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setEmptyHistoryText($emptyHistoryText)
    {
        return $this->setData(self::EMPTY_HISTORY_TEXT, $emptyHistoryText);
    }
}
