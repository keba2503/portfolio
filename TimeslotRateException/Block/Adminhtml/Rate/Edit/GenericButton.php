<?php

namespace Hiperdino\TimeslotRateException\Block\Adminhtml\Rate\Edit;

use Hiperdino\TimeslotRateException\Model\Data\RateFactory;
use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    protected Context $context;
    protected RateFactory $rateFactory;

    public function __construct(
        Context $context,
        RateFactory $rateFactory
    ) {
        $this->context = $context;
        $this->rateFactory = $rateFactory;
    }

    /**
     *
     * @return int|null
     */
    public function getTransactionId()
    {
        $id = $this->context->getRequest()->getParam('id');
        $transaction = $this->rateFactory->create()->load($id);
        if ($transaction && $transaction->getId()) {
            return $transaction->getId();
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
