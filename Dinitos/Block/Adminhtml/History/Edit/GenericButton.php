<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\History\Edit;

use Hiperdino\Dinitos\Model\Data\HistoryFactory;
use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    protected Context $context;
    protected HistoryFactory $historyFactory;

    /**
     * @param Context $context
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        Context $context,
        HistoryFactory $historyFactory
    ) {
        $this->context = $context;
        $this->historyFactory = $historyFactory;
    }

    /**
     * Return Transaction ID
     *
     * @return int|null
     */
    public function getTransactionId()
    {
        $id = $this->context->getRequest()->getParam('id');
        $transaction = $this->historyFactory->create()->load($id);
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
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
