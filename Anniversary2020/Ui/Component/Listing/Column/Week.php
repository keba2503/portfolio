<?php

namespace Hiperdino\Anniversary2020\Ui\Component\Listing\Column;

use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;

class Week extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                $weekKey = $item["week_id"];
                switch ($weekKey) {
                    case "week_1":
                        $weekKey = "Semana 1";
                        break;
                    case "week_2":
                        $weekKey = "Semana 2";
                        break;
                    case "week_3":
                        $weekKey = "Semana 3";
                        break;
                    case "week_4":
                        $weekKey = "Semana 4";
                        break;
                }

                $item[$this->getData('name')] = $weekKey;//$export_status;
            }
        }

        return $dataSource;
    }
}