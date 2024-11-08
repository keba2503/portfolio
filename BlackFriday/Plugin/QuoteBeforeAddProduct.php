<?php

namespace Hiperdino\BlackFriday\Plugin;

use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\App\Config\ScopeConfigInterface;

class QuoteBeforeAddProduct
{

    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Catalog\Model\Product $product
     * @param null|float|\Magento\Framework\DataObject $request
     * @param null|string $processMode
     * @throws \Exception
     */
    public function beforeAddProduct(
        $quote,
        $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        $isBlackFridayStore = (bool)$this->scopeConfig->getValue('hiperdino_blackfriday/website/is_bf', 'websites');
        if ($isBlackFridayStore) {
            if ($request === null) {
                $requestedQty = 1;
            } else {
                $requestedQty = is_numeric($request) ? $request : $request->getData('qty');
            }
            $requestedQty = (float)$requestedQty;
            $buyMaxQty = (float)$product->getData('blackfriday_max_buy_qty');
            if ($buyMaxQty && $requestedQty > $buyMaxQty) {
                $forMsg = intval($buyMaxQty);
                throw new \Exception(__("Black Friday: No puedes comprar más de {$forMsg} unidades de {$product->getName()}"));
            }
        }
    }
}
