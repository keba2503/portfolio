<?php

namespace Hiperdino\BlackFriday\Block\Modal;

use Magento\Framework\View\Element\Template;

class StoresModal extends Template {
	public function getBlackFridayAjaxUrl() {
		return $this->getUrl('hiperdino_blackfriday/index/login');
	}
}
