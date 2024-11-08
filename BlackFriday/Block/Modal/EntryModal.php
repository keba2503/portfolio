<?php

namespace Hiperdino\BlackFriday\Block\Modal;

use Hiperdino\BlackFriday\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class EntryModal extends Template {
	/**
	 * @var Config
	 */
	protected $blackFridayConfig;

	public function __construct (
		Context $context,
		Config $blackFridayConfig,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->blackFridayConfig = $blackFridayConfig;
	}

	public function getModalMessage() {
		return $this->blackFridayConfig->getEntryModalMessage();
	}
}
