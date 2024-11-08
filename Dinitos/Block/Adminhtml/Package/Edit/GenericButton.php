<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Package\Edit;

use Exception;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    public function __construct(
        protected Context $context,
        protected PackageRepository $packageRepository
    ) {
    }

    /**
     * Return Transaction ID
     * @throws Exception
     */
    public function getTransactionId(): ?int
    {
        $id = $this->context->getRequest()->getParam('id');
        $transaction = $this->packageRepository->getById($id);
        if ($transaction->getId()) {
            return $transaction->getId();
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
