<?php

namespace Hiperdino\Dinitos\Block;

use Hiperdino\Dinitos\Helper\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Singular\EcommerceApp\Helper\Cart;

class CartInfo extends Template
{
    protected Cart $ecommerceAppCartHelper;
    protected Config $dinitosConfig;

    /**
     * CartInfo constructor.
     * @param Cart $ecommerceAppCartHelper
     * @param Context $context
     * @param Config $dinitosConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Cart $ecommerceAppCartHelper,
        Config $dinitosConfig,
        array $data = []
    ) {
        $this->ecommerceAppCartHelper = $ecommerceAppCartHelper;
        parent::__construct($context, $data);
        $this->dinitosConfig = $dinitosConfig;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDinitosCmsBlock()
    {
        return $this->dinitosConfig->getDinitosCms();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function hasDinitosEnabled(): bool
    {
        return $this->isPickupDinitosEnabled() || $this->isPickupPointDinitosEnabled() || $this->isDeliveryDinitosEnabled();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function hasFreeShipping(): bool
    {
        return $this->hasPickupFree() || $this->hasPickupPointFree() || $this->hasDeliveryFree();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function hasDeliveryFree()
    {
        if ($this->isDeliveryDinitosEnabled()) {
            return $this->getDinitosLeftForFreeShipping('delivery');
        }

        return false;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function hasPickupFree()
    {
        if ($this->isPickupDinitosEnabled()) {
            return $this->getDinitosLeftForFreeShipping('pickup');
        }

        return false;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function hasPickupPointFree()
    {
        if ($this->isPickupPointDinitosEnabled()) {
            return $this->getDinitosLeftForFreeShipping('pickuppoint');
        }

        return false;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function isDeliveryDinitosEnabled()
    {
        return $this->dinitosConfig->getDinitosActive('delivery');
    }

    /**
     * @throws NoSuchEntityException
     */
    public function isPickupDinitosEnabled()
    {
        return $this->dinitosConfig->getDinitosActive('pickup');
    }

    /**
     * @throws NoSuchEntityException
     */
    public function isPickupPointDinitosEnabled()
    {
        return $this->dinitosConfig->getDinitosActive('pickuppoint');
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDinitosLeftForFreeShipping($deliveryMethod = 'delivery')
    {
        $totalDinitosQty = $this->ecommerceAppCartHelper->getQuote()->getData('total_dinitos_qty') ?: 0;
        $dinitosThreshold = (int)$this->dinitosConfig->getDinitosQty($deliveryMethod);

        return $dinitosThreshold - $totalDinitosQty;
    }
}
