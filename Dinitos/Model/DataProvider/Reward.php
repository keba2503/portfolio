<?php

namespace Hiperdino\Dinitos\Model\DataProvider;

use Exception;
use Hiperdino\Dinitos\Helper\Data;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Hiperdino\Dinitos\Model\ResourceModel\Reward\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Singular\Delivery\Model\Config\Source\DeliveryTypeOptions;

class Reward extends AbstractDataProvider
{
    protected RewardRepository $rewardRepository;
    protected RequestInterface $request;
    protected array $loadedData = [];
    protected $collection;
    protected Data $dataHelper;
    protected DeliveryTypeOptions $deliveryTypeOptions;

    public function __construct(
        RewardRepository $rewardRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Data $dataHelper,
        RequestInterface $request,
        DeliveryTypeOptions $deliveryTypeOptions,
        array $meta = [],
        array $data = []
    ) {
        $this->rewardRepository = $rewardRepository;
        $this->collection = $collectionFactory->create();
        $this->dataHelper = $dataHelper;
        $this->request = $request;
        $this->deliveryTypeOptions = $deliveryTypeOptions;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @throws Exception
     */
    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }
        $requestId = $this->request->getParam($this->requestFieldName);
        $object = $this->rewardRepository->getById($requestId);
        if ($object->getId()) {
            $dinitosData = $object->getData();
            $dinitosData['shipping_method'] = $dinitosData['entity_identifier'];
            $option = $this->setShippingMethodData($dinitosData);
            $dinitosData['shipping_options'] = $option ? array_key_first($option) : null;
            $this->loadedData[$object->getId()] = $dinitosData;
        }

        return $this->loadedData;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta['dinitos_reward'] = [
            'children' => [
                'entity_identifier' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'notice' => __(
                                    'Ingrese el identificador según el tipo de recompensa. ' .
                                    'Para productos, ingrese el SKU. ' .
                                    'Para descuentos, ingrese el código de promoción. ' .
                                    'Para envío, elija una de las siguientes opciones: ' .
                                    'Envío a domicilio, Recogida en tienda, Punto de recogida o Taquillas.'
                                )
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }

    private function setShippingMethodData($dinitosData)
    {
        if ($dinitosData['type'] == 1) {
            $options = $this->deliveryTypeOptions->getOptionArray();
            return array_filter($options, function($option) use ($dinitosData) {
                $optionToCheckWith = $dinitosData['entity_identifier'];
                if ($dinitosData['entity_identifier'] == 'Envío a domicilio') {
                    $optionToCheckWith = 'Envio a domicilio';
                }
                return $option == $optionToCheckWith;
            });
        }
    }
}
