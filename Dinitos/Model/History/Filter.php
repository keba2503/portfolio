<?php

namespace Hiperdino\Dinitos\Model\History;

use Hiperdino\Dinitos\Api\Data\FilterDinitosInterface;
use Magento\Framework\Api\AbstractExtensibleObject as AbstractExtensibleObject;

class Filter extends AbstractExtensibleObject implements FilterDinitosInterface
{
    public function getTitleNative()
    {
        return $this->_get(self::TITLE_NATIVE);
    }

    public function setTitleNative(string $titleNative)
    {
        return $this->setData(self::TITLE_NATIVE, $titleNative);
    }

    public function getTitleCustom()
    {
        return $this->_get(self::TITLE_CUSTOM);
    }

    public function setTitleCustom(string $titleCustom)
    {
        return $this->setData(self::TITLE_CUSTOM, $titleCustom);
    }
}
