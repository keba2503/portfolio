<?php

namespace Hiperdino\BlackFriday\Block\Modal;

use Magento\Framework\View\Element\Template;

class WarningModal extends Template {
	public function blackFridayLoginAjaxUrl() {
		return $this->getUrl('hiperdino_blackfriday/index/login');
	}

	public function blackFridayLogoutAjaxUrl() {
		return $this->getUrl('hiperdino_blackfriday/index/logout');
	}
}
