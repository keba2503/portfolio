<?php

namespace Hiperdino\Anniversary\Block\Order\Totals;

use Hiperdino\Anniversary\Helper\ExtraInfo;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Totals;

class Anniversary extends Totals
{
    protected ExtraInfo $extraInfo;
    protected $anniversaryInfo;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtraInfo $extraInfo,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->extraInfo = $extraInfo;
        $this->anniversaryInfo = false;
    }

    public function showAnniversaryInfo()
    {
        return (bool)$this->getAnniversaryInfo();
    }

    public function getAnniversaryInfo()
    {
        if (!$this->anniversaryInfo) {
            $this->anniversaryInfo = $this->extraInfo->getAnniversaryInfo($this->getOrder()->getData("extra_info"));
        }

        return $this->anniversaryInfo;
    }
}
