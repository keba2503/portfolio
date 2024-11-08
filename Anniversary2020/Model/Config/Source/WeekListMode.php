<?php

namespace Hiperdino\Anniversary2020\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class WeekListMode implements OptionSourceInterface
{
    const WEEK_1 = "week_1";
    const WEEK_2 = "week_2";
    const WEEK_3 = "week_3";
    const WEEK_4 = "week_4";

    const WEEK_1_LABEL = "Semana 1";
    const WEEK_2_LABEL = "Semana 2";
    const WEEK_3_LABEL = "Semana 3";
    const WEEK_4_LABEL = "Semana 4";

    public function toOptionArray()
    {
        return [
            ['value' => self::WEEK_1, 'label' => self::WEEK_1_LABEL],
            ['value' => self::WEEK_2, 'label' => self::WEEK_2_LABEL],
            ['value' => self::WEEK_3, 'label' => self::WEEK_3_LABEL],
            ['value' => self::WEEK_4, 'label' => self::WEEK_4_LABEL],
        ];
    }
}