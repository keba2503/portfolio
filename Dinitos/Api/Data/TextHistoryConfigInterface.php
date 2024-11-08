<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * @api
 */
interface TextHistoryConfigInterface extends CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const MAIN_TITLE = 'main_title';
    const TITLE = 'title';
    const TEXT_TO_EXPIRE = 'text_to_expire';
    const TEXT_URL_ONBOARDING = 'text_url_onboarding';
    const URL_ONBOARDING = 'cms_block_id_onboarding';
    const TEXT_LINK_SIDEBAR = 'text_link_sidebar';
    const ICON_SIDEBAR = 'icon_sidebar';
    const EMPTY_HISTORY_TEXT = 'empty_history_text';
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
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

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
    public function getTextUrlOnboarding();

    /**
     * @param string $textUrlOnboarding
     * @return $this
     */
    public function setTextUrlOnboarding($textUrlOnboarding);

    /**
     * @return string
     */
    public function getCmsBlockIdOnboarding();

    /**
     * @param string $urlOnboarding
     * @return $this
     */
    public function setCmsBlockIdOnboarding($urlOnboarding);

    /**
     * @return string
     */
    public function getTextLinkSidebar();

    /**
     * @param string $textLinkSidebar
     * @return $this
     */
    public function setTextLinkSidebar($textLinkSidebar);

    /**
     * @return string
     */
    public function getIconSidebar();

    /**
     * @param string $iconSidebar
     * @return $this
     */
    public function setIconSidebar($iconSidebar);

    /**
     * @return mixed
     */
    public function getEmptyHistoryText();

    /**
     * @param $emptyHistoryText
     * @return mixed
     */
    public function setEmptyHistoryText($emptyHistoryText);
}
