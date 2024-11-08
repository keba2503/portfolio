<?php

namespace Hiperdino\BlackFriday\Block\Email\Sales;

use Exception;
use Hiperdino\BlackFriday\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\OrderFactory;

class BlackFriday extends Template
{
    /**
     * @var Data
     */
    protected $blackFridayHelper;
    protected $blackFridayInfo;
    protected OrderFactory $orderFactory;
    protected $order;

    public function __construct(
        Template\Context $context,
        Data $blackFridayHelper,
        OrderFactory $orderFactory,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->blackFridayHelper = $blackFridayHelper;
        $this->blackFridayInfo = null;
        $this->orderFactory = $orderFactory;
        $this->order = null;
    }

    public function getOrder()
    {
        try {
            if (!$this->order) {
                $incrementId = $this->getData('order') ?: $this->_request->getParam("increment_id");
                if ($incrementId === null) throw new Exception(__('El id del pedido es incorrecto'));
                $this->order = $this->orderFactory->create()->loadByIncrementId($incrementId);
            }

            return $this->order;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getBlackFridayInfo()
    {
        if (is_null($this->blackFridayInfo)) {
            $order = $this->getOrder();
            if ($order) {
                $extraInfo = $order->getData("extra_info");
                $this->blackFridayInfo = $this->blackFridayHelper->getBlackFridayInfo($extraInfo);
            }
        }

        return $this->blackFridayInfo;
    }

    public function hasBlackFridayInfo()
    {
        $blackFriday = $this->getBlackFridayInfo();

        return (bool)$blackFriday;
    }

    public function getExistBlackFridayDiscount()
    {
        if (!is_null($this->blackFridayInfo) && isset($this->blackFridayInfo["exist_blackfriday_discount"])) {
            return $this->blackFridayInfo["exist_blackfriday_discount"];
        }

        return false;
    }

    public function getLabeInfoCheckout()
    {
        if (!is_null($this->blackFridayInfo) && isset($this->blackFridayInfo["label_info_checkout"])) {
            return $this->blackFridayInfo["label_info_checkout"];
        }

        return "";
    }

    public function getInfoCheckout()
    {
        if (!is_null($this->blackFridayInfo) && isset($this->blackFridayInfo["info_checkout"])) {
            return $this->blackFridayInfo["info_checkout"];
        }

        return "";
    }
}
