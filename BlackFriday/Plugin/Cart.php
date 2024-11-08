<?php

namespace Hiperdino\BlackFriday\Plugin;

use Exception;
use Hiperdino\BlackFriday\Helper\BlackFriday2020 as BlackFridayHelper;
use Hiperdino\BlackFriday\Helper\Config as Config;
use Magento\Checkout\Model\Cart as Quote;

class Cart {
	protected $blackFridayHelper;
	protected $config;

	public function __construct(
		BlackFridayHelper $blackFridayHelper,
		Config $config
	) {
		$this->blackFridayHelper = $blackFridayHelper;
		$this->config = $config;
	}

	/**
	 * @param Quote $quote
	 * @param $procede
	 * @return mixed
	 * @throws Exception
	 */
	public function aroundSave(Quote $quote, $procede) {
		// Check Black Friday quote max items
		if ($this->blackFridayHelper->isBlackFridayStore()) {
			$quoteItems = 0.0;
			foreach ($quote->getQuote()->getAllVisibleItems() as $item) $quoteItems += (float)$item->getQty();
			$maxItems = $this->config->getMaxQuoteItems();
			if ($maxItems && $quoteItems > $maxItems) {
				throw new Exception(__('Lo sentimos, pero no puedes sobrepasar el límite de ' . $maxItems . ' artículos para pedidos en Black Friday'), 37);
			}
		}

		return $procede();
	}
}
