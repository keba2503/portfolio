<?php

namespace Hiperdino\Dinitos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreRepository;
use Magento\Ui\Component\Listing\Columns\Column;

class StoresOptions extends Column implements OptionSourceInterface

{
    protected array $options;
    protected StoreRepository $storeRepository;
    protected string $storeKey;

    public function __construct(
        StoreRepository $storeRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        string $storeKey = 'store_id',
        array $data = [],
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeKey = $storeKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = $this->prepareItem($item);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function prepareItem(array $item): string
    {
        $content = "";

        $storeViewsIds = explode(',', ($item[$this->getData('name')]));
        foreach ($storeViewsIds as $storeViewId) {
            $storeView = $this->storeRepository->getById($storeViewId);
            $content .= $storeView->getName() . ", ";
        }

        return mb_substr($content, 0, -2);
    }

    public function toOptionArray(): array
    {
        return $this->getAllOptions();
    }

    public function getAllOptions(): array
    {
        foreach ($this->storeRepository->getList() as $storeView) {
            $this->options[] = ['label' => $storeView->getName(), 'value' => $storeView->getId()];
        }

        return $this->options;
    }
}
