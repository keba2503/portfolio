<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * @api
 */
interface FilterDinitosInterface extends CustomAttributesDataInterface
{
    const TITLE_NATIVE = 'title_native';
    const TITLE_CUSTOM = 'title_custom';

    /**
     * @return string
     */
    public function getTitleNative();

    /**
     * @param string $titleNative
     * @return $this
     */
    public function setTitleNative(string $titleNative);

    /**
     * @return string
     */
    public function getTitleCustom();

    /**
     * @param string $titleCustom
     * @return $this
     */
    public function setTitleCustom(string $titleCustom);
}
