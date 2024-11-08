<?php

namespace Hiperdino\TimeslotRateException\Block\Checkout;

use Hiperdino\TimeslotRateException\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Timeslot extends Template
{
    protected Config $config;

    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    public function showTimeslotPrice()
    {
        return $this->config->getShowTimeslotPrice();
    }
}
