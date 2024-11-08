<?php

namespace Hiperdino\Anniversary\Helper;

use DateTime;
use Exception;
use Hiperdino\Anniversary\Helper\Config as AnniversaryConfig;
use Hiperdino\Anniversary\Model\Participation\ParticipationCalculator;
use Hiperdino\Catalog\Helper\Attribute;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Store\Model\StoreRepository;

class Update
{
    const ANNIVERSARY_BDTABLE = "hiperdino_anniversary_product";

    protected ResourceConnection $resourceConnection;
    protected Action $productAction;
    protected StoreRepository $storeRepository;
    protected ProductRepository $productRepository;
    protected State $state;
    protected Config $eavConfig;
    protected array $stores;
    protected array $products;
    protected $anniversaryTagId;
    protected Attribute $hdAttributeHelper;
    protected Logger $logger;
    protected Grouped $groupedProductModel;
    protected IndexerFactory $indexerFactory;
    protected ParticipationCalculator $anniversaryHelper;
    protected AnniversaryConfig $anniversaryConfig;

    public function __construct(
        ResourceConnection $resourceConnection,
        Action $productAction,
        StoreRepository $storeRepository,
        ProductRepository $productRepository,
        State $state,
        Config $eavConfig,
        Grouped $groupedProductModel,
        IndexerFactory $indexerFactory,
        ParticipationCalculator $anniversaryHelper,
        Attribute $hdAttributeHelper,
        Logger $logger,
        AnniversaryConfig $anniversaryConfig
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->productAction = $productAction;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->eavConfig = $eavConfig;
        $this->groupedProductModel = $groupedProductModel;
        $this->stores = [];
        $this->products = [];
        $this->indexerFactory = $indexerFactory;
        $this->anniversaryHelper = $anniversaryHelper;
        $this->hdAttributeHelper = $hdAttributeHelper;
        $this->logger = $logger;
        $this->anniversaryConfig = $anniversaryConfig;
    }

    public function processUpdate()
    {
        $todayDate = date('Y-m-d');
        $today = new DateTime();
        $today->setTime(23, 59, 59);
        $todayIncluded = $today->format('Y-m-d H:i:s');
        $today->setTime(00, 00, 00);
        $todayExcluded = $today->format('Y-m-d H:i:s');

        $this->_getAttributeOptionId();

        $anniversaryDeleteData = $this->fetchAnniversaryDeleteData($todayExcluded);
        $anniversaryUpdateData = $this->_fetchAnniversaryUpdateData($todayIncluded, $todayExcluded);

        if (sizeof($anniversaryDeleteData) > 0 || sizeof($anniversaryUpdateData) > 0) {
            $this->logAnniversary("*************** Comienza la carga de anniversary - " . $todayDate . " ***");

            if (!$this->anniversaryTagId) {
                $this->logAnniversary("ANNIVERSARY TAG ID - " . $this->anniversaryTagId);

                return;
            }

            // 1º Borro los registros antiguos y pongo los productos a 0 anniversary
            $this->logAnniversary("ANNIVERSARY A BORRAR - " . sizeof($anniversaryDeleteData));

            foreach ($anniversaryDeleteData as $anniversaryDelete) {
                try {
                    $this->processAnniversaryData($anniversaryDelete, true);
                    $this->_deleteRecord($anniversaryDelete["id"]);
                } catch (Exception $e) {
                    $this->logAnniversary("Error al borrar anniversary del producto: " . $anniversaryDelete['product_sku'] . ". Error " . $e->getMessage());
                }
            }

            // 2º Actualizo los nuevos anniversary
            $this->logAnniversary("ANNIVERSARY A ACTUALIZAR - " . sizeof($anniversaryUpdateData));

            foreach ($anniversaryUpdateData as $anniversaryUpdate) {
                try {
                    $processed = $this->processAnniversaryData($anniversaryUpdate);
                    $this->_markRecordAsProcessed($anniversaryUpdate["id"], $processed);
                } catch (Exception $e) {
                    $this->logAnniversary("Error al actualizar anniversary del producto: " . $anniversaryUpdate['product_sku'] . ". Error " . $e->getMessage());
                }
            }

            // 3º FIX para la tienda 0
            $this->hdAttributeHelper->fixToDefaultStore('product_tags');

            // 4º Reindexo si es necesario
            try {
                //  $this->reindex("catalog_product_attribute");
            } catch (Exception $e) {
                $this->logAnniversary("Error al reindexar: " . $e->getMessage());
            }

            $this->logAnniversary("*************** Termina la carga de anniversary *****************");
        }
    }

    protected function processAnniversaryData($anniversaryData, $delete = false)
    {
        // Tienda
        if (!isset($this->stores[$anniversaryData['store']])) {
            try {
                $store = $this->storeRepository->get($anniversaryData['store']);
                $this->stores[$anniversaryData['store']] = $store;
            } catch (Exception $e) {
                return false;
            }
        }
        $store = $this->stores[$anniversaryData['store']];

        // Producto
        if (!isset($this->products[$anniversaryData['product_sku']])) {
            try {
                $product = $this->productRepository->get($anniversaryData['product_sku']);
                $this->products[$anniversaryData['product_sku']] = $product;
            } catch (Exception $e) {
                return false;
            }
        } else {
            $product = $this->products[$anniversaryData['product_sku']];
        }

        // Establecemos datos
        $hasAnniversary = !$delete ? 1 : 0;
        $productTags = explode(',', $product->getData('product_tags') ?: "");
        if ($hasAnniversary) {
            if (!in_array($this->anniversaryTagId, $productTags)) {
                $productTags[] = $this->anniversaryTagId;
            }
        } else {
            if (in_array($this->anniversaryTagId, $productTags)) {
                $key = array_search($this->anniversaryTagId, $productTags);
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
                        array_push($productsIds, $id);
                    }
                } else {
                    array_push($productsIds, $child);
                }
            }
        }

        try {
            // Actualizamos atributos
            $this->productAction->updateAttributes(
                $productsIds,
                [
                    'product_tags' => $newProductTags
                ],
                $store->getId()
            );

        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    protected function _fetchAnniversaryUpdateData($fromDate, $toDate)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $sql = "SELECT * FROM " . self::ANNIVERSARY_BDTABLE . " WHERE from_date <= ? AND to_date >= ? AND processed = 0";

            return $connection->fetchAll($sql, array($fromDate, $toDate));
        } catch (Exception $e) {
            $this->logAnniversary("Error al extraer los anniversary a actualizar de la tabla. Error " . $e->getMessage());

            return [];
        }
    }

    private function fetchAnniversaryDeleteData($toDate)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $sql = "SELECT * FROM " . self::ANNIVERSARY_BDTABLE . "  WHERE to_date < ?";

            return $connection->fetchAll($sql, array($toDate));
        } catch (Exception $e) {
            $this->logAnniversary("Error al extraer los anniversary a borrar de la tabla. Error " . $e->getMessage());

            return [];
        }
    }

    protected function _deleteRecord($id)
    {
        $connection = $this->resourceConnection->getConnection();
        $query = "DELETE FROM " . self::ANNIVERSARY_BDTABLE . " WHERE id = ?";
        $connection->query($query, [$id]);
    }

    protected function _markRecordAsProcessed($id, $processed = true)
    {
        $connection = $this->resourceConnection->getConnection();
        if ($processed) $query = "UPDATE " . self::ANNIVERSARY_BDTABLE . " SET processed=1, is_active = 1 WHERE id = ?";
        else $query = "UPDATE " . self::ANNIVERSARY_BDTABLE . " SET processed=1, is_active = 0 WHERE id = ?";
        $connection->query($query, [$id]);
    }

    protected function _getAttributeOptionId()
    {
        $this->anniversaryTagId = $this->anniversaryConfig->getRascaTag();
    }

    private function logAnniversary($message)
    {
        $this->logger->logUpdateAnniversary($message);
    }

}
