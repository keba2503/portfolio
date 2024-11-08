<?php

namespace Hiperdino\Anniversary2020\Ui\Component\Listing\Column\Participation;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    const ERROR_STATUS = 2;

    protected UrlInterface $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['status'] == self::ERROR_STATUS) {
                    $item[$this->getData('name')]['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'hiperdino_anniversary2020/queue/MassReset',
                            ['queue_id' => $item['id']]
                        ),
                        'label' => __('Marcar como pendiente')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
