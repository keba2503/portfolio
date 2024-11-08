<?php

namespace Hiperdino\TimeslotRateException\Controller\Checkout;

use Hiperdino\TimeslotRateException\Helper\Config;
use Hiperdino\TimeslotRateException\Model\Services\CustomerSelectedTimeslot;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Singular\EcommerceApp\Helper\Cart;

class SelectTimeslot extends Action
{

    public function __construct(
        Context $context,
        protected readonly JsonFactory $resultJsonFactory,
        protected readonly Validator $formKeyValidator,
        protected readonly Session $customerSession,
        protected readonly CustomerSelectedTimeslot $customerSelectedTimeslot,
        protected readonly Cart $ecommerceAppCartHelper,
        protected readonly Config $config
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $resultJson->setData(['success' => -1]);
        }

        $timeslotId = $this->getRequest()->getParam('timeslot_id');
        if ($timeslotId) {
            $selectedDate = $this->getRequest()->getParam('timeslot_date');
            $selectedDate = $this->transformToDate($selectedDate);
            $this->customerSelectedTimeslot->setTimeslot($timeslotId, $selectedDate);
            $quote = $this->ecommerceAppCartHelper->getQuote();
            $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
            $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
        }

        $resultJson->setData(['success' => 1]);

        return $resultJson;
    }

    /**
     * @param mixed $selectedDate
     * @return string
     */
    protected function transformToDate(mixed $selectedDate): string
    {
        $selectedDate = substr_replace($selectedDate, '-', 4, 0);
        $selectedDate = substr_replace($selectedDate, '-', 2, 0);

        return date("Y/m/d", strtotime($selectedDate));
    }
}
