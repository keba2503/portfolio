<?php

namespace Hiperdino\BlackFriday\Controller\Index;

use Hiperdino\BlackFriday\Helper\BlackFriday2020 as BlackFridayHelper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Login extends Action {
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
			// Get Black Friday postcode
			if (!$blackFridayPostcode = $this->getRequest()->getParam('postcode', false)) {
				throw new \Exception(__('No se ha especificado una tienda Black Friday'), 1);
			}

			// Get Black Friday store ID
			if (!$blackFridayStoreId = $this->getRequest()->getParam('store', false)) {
				throw new \Exception(__('No se ha especificado una tienda Black Friday'), 1);
			}

			// Check request, postcode cookie or customer data
			$postcode = $this->blackFridayHelper->getAccessPostcode();

			// Customer has active quote? Empty it
			$this->cart->truncate()->save();

			// Set real postcode cookie
			$this->blackFridayHelper->setRealPostcode($postcode);

			$this->blackFridayHelper->changeStore($blackFridayPostcode, $blackFridayStoreId);
			$result['error'] = 0;
		} catch (\Exception $e) {
			$result['error'] = 1;
			$result['errorCode'] = $e->getCode();
			$result['message'] = $e->getMessage();
		}

		return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
	}
}
