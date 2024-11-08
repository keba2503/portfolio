<?php

namespace Hiperdino\Anniversary2020\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class PrizeReport extends Template
{
    protected Session $session;

    public function __construct(
        Template\Context $context,
        Session $session,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->session = $session;
    }

    public function getFormUrl()
    {
        return $this->getUrl('*/*/*');
    }

    public function getDeactivateCouponUrl($couponCode)
    {
        return $this->getUrl('*/*/deactivatePost', ['coupon_code' => $couponCode, 'customer_id' => $this->_request->getParam('customer_id')]);
    }

    public function getCustomerParticipations()
    {
        return $this->session->getCustomerParticipations() ?: [];
    }
}