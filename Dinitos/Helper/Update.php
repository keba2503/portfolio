<?php

namespace Hiperdino\Dinitos\Helper;

use Exception;
use Hiperdino\Catalog\Helper\Attribute;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\StoreRepository;

class Update
{
    const DINITOS_BDTABLE = "hiperdino_dinitos";

    protected ResourceConnection $resourceConnection;
    protected Action $productAction;
    protected StoreRepository $storeRepository;
    protected ProductRepository $productRepository;
    protected EavConfig $eavConfig;
    protected array $stores;
    protected array $products;
    protected $dinitoTagId;
    protected Grouped $groupedProductModel;
    protected Attribute $hdAttributeHelper;
    protected Logger $logger;

    public function __construct(
        ResourceConnection $resourceConnection,
        Action $productAction,
        StoreRepository $storeRepository,
        ProductRepository $productRepository,
        EavConfig $eavConfig,
        Grouped $groupedProductModel,
        Attribute $hdAttributeHelper,
        Logger $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->productAction = $productAction;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->eavConfig = $eavConfig;
        $this->groupedProductModel = $groupedProductModel;
        $this->stores = [];
        $this->products = [];
        $this->hdAttributeHelper = $hdAttributeHelper;
        $this->logger = $logger;
    }

    public function processUpdate(): void
    {
        $today = date('Y-m-d');
        $this->getAttributeOptionIdByLabel('product_tags', 'Dinitos');
        $dinitosDeleteData = $this->fetchDinitosDeleteData($today);
        $dinitosUpdateData = $this->fetchDinitosUpdateData($today);

        if (sizeof($dinitosDeleteData) > 0 || sizeof($dinitosUpdateData) > 0) {
            $this->logDinitos("*************** Comienza la carga de dinitos - " . $today . " ***");

            if (!$this->dinitoTagId) {
                $this->logDinitos("DINITO TAG ID - " . $this->dinitoTagId);

                return;
            }
            // 1º Borro los registros antiguos y pongo los productos a 0 dinitos
            $this->logDinitos("DINITOS A BORRAR - " . sizeof($dinitosDeleteData));

            foreach ($dinitosDeleteData as $dinitosDelete) {
                try {
                    $this->deleteRecord($dinitosDelete["id"]);
                } catch (Exception $e) {
                    $this->logDinitos("Error al borrar dinitos del producto: " . $dinitosDelete['product_sku'] . ". Error " . $e->getMessage());
                }
            }
            // 2º Actualizo los nuevos dinitos
            $this->logDinitos("DINITOS A ACTUALIZAR - " . sizeof($dinitosUpdateData));

            foreach ($dinitosUpdateData as $dinitoUpdate) {
                try {
                    $processed = $this->processDinitosData($dinitoUpdate);
                    $this->markRecordAsProcessed($dinitoUpdate["id"], $processed);
                } catch (Exception $e) {
                    $this->logDinitos("Error al actualizar dinitos del producto: " . $dinitoUpdate['product_sku'] . ". Error " . $e->getMessage());
                }
            }

            // 3º FIX para la tienda 0
            $this->hdAttributeHelper->fixToDefaultStore('product_tags');
            $this->logDinitos("*************** Termina la carga de dinitos *****************");
        }
    }

    protected function processDinitosData($dinitosData): bool
    {
        // Tienda
        if (!isset($this->stores[$dinitosData['store']])) {
            try {
                $store = $this->storeRepository->get($dinitosData['store']);
                $this->stores[$dinitosData['store']] = $store;
            } catch (Exception $e) {
                return false;
            }
        }
        $store = $this->stores[$dinitosData['store']];
        // Producto
        if (!isset($this->products[$dinitosData['product_sku']])) {
            try {
                $product = $this->productRepository->get($dinitosData['product_sku']);
                $this->products[$dinitosData['product_sku']] = $product;
            } catch (Exception $e) {
                return false;
            }
        }
        $product = $this->products[$dinitosData['product_sku']];

        if ($dinitosData['gramos'] > 0) {
            $dinitosQty = 100;
        } else {
            $dinitosQty = $dinitosData['dinitos'];
        }

        // Establecemos datos
        $hasDinitos = $dinitosQty > 0 ? 1 : 0;
        $productTags = explode(',', $product->getData('product_tags') ?: "");
        if ($hasDinitos) {
            if (!in_array($this->dinitoTagId, $productTags)) {
                $productTags[] = $this->dinitoTagId;
            }
        } else {
            if (in_array($this->dinitoTagId, $productTags)) {
                $key = array_search($this->dinitoTagId, $productTags);
                unset($productTags[$key]);
            }
        }
        $newProductTags = implode(',', $productTags ?: []);
        $productsIds = [$product->getId()];

        if ($product->getTypeId() === GROUPED::TYPE_CODE) {
            $childrenIds = $this->groupedProductModel->getChildrenIds($product->getId());

            foreach ($childrenIds as $child) {
                if (is_array($child)) {
                    foreach ($child as $id) {
                        $productsIds[] = $id;
                    }
                } else {
                    $productsIds[] = $child;
                }
            }
        }
        try {
            // Actualizamos atributos
            $this->productAction->updateAttributes(
                $productsIds,
                [
                    'has_dinitos' => $hasDinitos,
                    'dinitos_qty' => $dinitosQty,
                    'product_tags' => $newProductTags
                ],
                $store->getId()
            );

        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    protected function fetchDinitosUpdateData($today): array
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $sql = "SELECT * FROM " . self::DINITOS_BDTABLE . " WHERE from_date <= ? AND to_date >= ? AND processed = 0";

            return $connection->fetchAll($sql, array($today, $today));
        } catch (Exception $e) {
            $this->logDinitos("Error al extraer los dinitos a actualizar de la tabla. Error " . $e->getMessage());

            return [];
        }
    }

    private function fetchDinitosDeleteData($today): array
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $sql = "SELECT * FROM " . self::DINITOS_BDTABLE . "  WHERE to_date < ?";

            return $connection->fetchAll($sql, array($today));
        } catch (Exception $e) {
            $this->logDinitos("Error al extraer los dinitos a borrar de la tabla. Error " . $e->getMessage());

            return [];
        }
    }

    protected function deleteRecord($id): void
    {
        $connection = $this->resourceConnection->getConnection();
        $query = "DELETE FROM " . self::DINITOS_BDTABLE . " WHERE id = ?";
        $connection->query($query, [$id]);
    }

    protected function markRecordAsProcessed($id, $processed = true): void
    {
        $connection = $this->resourceConnection->getConnection();
        if ($processed) {
            $query = "UPDATE " . self::DINITOS_BDTABLE . " SET processed=1, is_active = 1 WHERE id = ?";
        } else {
            $query = "UPDATE " . self::DINITOS_BDTABLE . " SET processed=1, is_active = 0 WHERE id = ?";
        }
        $connection->query($query, [$id]);
    }

    protected function getAttributeOptionIdByLabel($attributeCode, $optionLabel): void
    {
        try {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
            $options = $attribute->getSource()->getAllOptions();
            foreach ($options as $option) {
                if ($option['label'] == $optionLabel) {
                    $this->dinitoTagId = $option['value'];

                    return;
                }
            }
        } catch (Exception $e) {
            $this->logDinitos("Attribute option by id error: {$e->getMessage()}");
        }
        $this->dinitoTagId = 0;
    }

    private function logDinitos($message): void
    {
        $this->logger->logUpdateDinitos($message);
    }
}
