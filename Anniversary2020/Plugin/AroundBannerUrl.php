<?php

namespace Hiperdino\Anniversary2020\Plugin;

use Closure;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Ux\Block\Home\Element;
use Singular\Home\Model\Home as HomeModel;

class AroundBannerUrl
{

    protected Config $anniversaryConfig;

    public function __construct(
        Config $anniversaryConfig
    ) {
        $this->anniversaryConfig = $anniversaryConfig;
    }

    public function aroundGetBannerUrl(
        Element $subject,
        Closure $proceed,
        $element
    ) {
        if ($element->getDataType() === HomeModel::DATA_TYPE_BANNER_ANNIVERSARY) {
            return $subject->getUrl($this->anniversaryConfig->getUrlCmsAnniversary());
        } else {
            return $proceed($element);
        }
    }
}
