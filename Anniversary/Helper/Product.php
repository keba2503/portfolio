<?php

namespace Hiperdino\Anniversary\Helper;

class Product
{
    protected Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function isAnniversaryProduct($tags)
    {
        $tagsArray = explode(",", $tags ?: "");

        if (in_array($this->config->getRascaTag(), $tagsArray)) return true;

        return false;
    }

}
