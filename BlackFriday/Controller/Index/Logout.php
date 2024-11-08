<?php

namespace Hiperdino\BlackFriday\Controller\Index;

use Hiperdino\BlackFriday\Helper\BlackFriday2020 as BlackFridayHelper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Logout extends Action {
	protected $jsonHelper;
	protected $cart;
	protected $blackFridayHelper;

	public function __construct(
		Context $context,
		JsonHelper $jsonHelper,
		Cart $cart,
		BlackFridayHelper $blackFridayHelper
	) {
		$this->jsonHelper = $jsonHelper;
		$this->cart = $cart;
		$this->blackFridayHelper = $blackFridayHelper;
		parent::__construct($context);
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		if (!$this->getRequest()->isAjax()) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('/');

			return $resultRedirect;
		}

		$result = [];

		try {
			// Check real postcode
			$postcode = $this->blackFridayHelper->getRealPostcode();
			if (!$postcode) throw new \Exception(__('No se ha encontrado el código postal real'), 4);

			// Customer has active quote? Empty it
			$this->cart->truncate()->save();

			// Delete real postcode
			$this->blackFridayHelper->unsRealPostcode();

			$this->blackFridayHelper->changeStore($postcode);
			$result['error'] = 0;
		} catch (\Exception $e) {
			$result['error'] = 1;
			$result['errorCode'] = $e->getCode();
			$result['message'] = $e->getMessage();
		}

		return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
	}
}
