<?php

namespace Hiperdino\TimeslotRateException\Block\Adminhtml\Exception\Edit;

use Hiperdino\TimeslotRateException\Model\Data\ExceptionFactory;
use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    protected Context $context;
    protected ExceptionFactory $exceptionFactory;

    public function __construct(
        Context $context,
        ExceptionFactory $exceptionFactory
    ) {
        $this->context = $context;
        $this->exceptionFactory = $exceptionFactory;
    }

    /**
     *
     * @return int|null
     */
    public function getTransactionId()
    {
        $id = $this->context->getRequest()->getParam('id');
        $exception = $this->exceptionFactory->create()->load($id);
        if ($exception && $exception->getId()) {
            return $exception->getId();
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
