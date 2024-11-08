<?php

namespace Hiperdino\BlackFriday\Helper;

use Magento\GroupedProduct\Model\Product\Type\Grouped;

class Update
{

    const BLACKFRIDAY_BDTABLE = "hiperdino_blackfriday_product";

    protected $_resourceConnection;
    protected $_productAction;
    protected $_storeRepository;
    protected $_productRepository;
    protected $_state;
    protected $_eavConfig;

    protected $_stores;
    protected $_products;

    protected $blackfridayTagId;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    private $_groupedProductModel;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    private $_indexerFactory;
    /**
     * @var \Hiperdino\BlackFriday\Helper\Data
     */
    private $_blackFridayHelper;
    /**
     * @var \Hiperdino\Catalog\Helper\Attribute
     */
    protected $_hdAttributeHelper;
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\State $state,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProductModel,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Hiperdino\BlackFriday\Helper\Data $blackfridayHelper,
        \Hiperdino\Catalog\Helper\Attribute $hdAttributeHelper,
        Logger $logger
    ) {
        $this->_resourceConnection = $resourceConnection;
        $this->_productAction = $productAction;
        $this->_storeRepository = $storeRepository;
        $this->_productRepository = $productRepository;
        $this->_state = $state;
        $this->_eavConfig = $eavConfig;
        $this->_groupedProductModel = $groupedProductModel;
        $this->_stores = [];
        $this->_products = [];
        $this->_indexerFactory = $indexerFactory;
        $this->_blackFridayHelper = $blackfridayHelper;
        $this->_hdAttributeHelper = $hdAttributeHelper;
        $this->logger = $logger;
    }

    public function processUpdate()
    {
        $today    = date('Y-m-d');

        $this->blackfridayTagId = $this->_getAttributeOptionId();
        $blackfridayDeleteData = $this->_fetchBlackFridayDeleteData($today);
        $blackfridayUpdateData = $this->_fetchBlackFridayUpdateData($today);

        if(sizeof($blackfridayDeleteData) > 0 || sizeof($blackfridayUpdateData) > 0){
            $this->logBlackFriday("*************** Comienza la carga de Black Friday - ".$today." ***");

            if(! $this->blackfridayTagId) {
                $this->logBlackFriday("NO ENCUENTRA EL BLACKFRIDAY TAG ID");
                return;
            }

            // 1º Borro los registros antiguos y pongo los productos a 0 blackfriday
            $this->logBlackFriday("BLACKFRIDAY A BORRAR - ".sizeof($blackfridayDeleteData));

            foreach ($blackfridayDeleteData as $blackfridayDelete){
                try{
                    $this->processBlackFridayData($blackfridayDelete, true);
                    $this->_deleteRecord($blackfridayDelete["id"]);
                }catch (\Exception $e){
                    $this->logBlackFriday("Error al borrar black friday del producto: ".$blackfridayDelete['product_sku'].". Error ".$e->getMessage());
                }
            }

            // 2º Actualizo los nuevos blackfriday
            $this->logBlackFriday("BLACKFRIDAY A ACTUALIZAR - ".sizeof($blackfridayUpdateData));

            foreach($blackfridayUpdateData as $blackfridayUpdate) {
                try{
                    $processed = $this->processBlackFridayData($blackfridayUpdate);
                    $this->_markRecordAsProcessed($blackfridayUpdate["id"], $processed);
                }catch (\Exception $e){
                    $this->logBlackFriday("Error al actualizar blackfriday del producto: ".$blackfridayUpdate['product_sku'].". Error ".$e->getMessage());
                }
            }

            $this->_hdAttributeHelper->fixToDefaultStore('product_tags');

            // 4º Reindexo si es necesario
            try{
              //  $this->reindex("catalog_product_attribute");
            }catch (\Exception $e){
                $this->logBlackFriday("Error al reindexar: ".$e->getMessage());
            }

            $this->logBlackFriday("*************** Termina la carga de blackfriday *****************");
        }
    }

    protected  function processBlackFridayData($blackfridayData, $delete = false){
        // Tienda
        if(! isset($this->_stores[$blackfridayData['store']])) {
            try {
                $store = $this->_storeRepository->get($blackfridayData['store']);
                $this->_stores[$blackfridayData['store']] = $store;
            } catch(\Exception $e) {
                return false;
            }
        }
        $store = $this->_stores[$blackfridayData['store']];

        // Producto
        if(! isset($this->_products[$blackfridayData['product_sku']])) {
            try {
                $product = $this->_productRepository->get($blackfridayData['product_sku'], true, $store->getId());
                $this->_products[$blackfridayData['product_sku']] = $product;
            } catch(\Exception $e) {
                return false;
            }
        } else {
            $product = $this->_products[$blackfridayData['product_sku']];
        }

        // Establecemos datos
        $hasBlackFriday = !$delete ? 1 : 0;
        $productTags = explode(',', $product->getData('product_tags') ?: "");
        if($hasBlackFriday) {
            if(! in_array($this->blackfridayTagId, $productTags)) {
                $productTags[] = $this->blackfridayTagId;
            }
        } else {
            if(in_array($this->blackfridayTagId, $productTags)) {
                $key = array_search($this->blackfridayTagId, $productTags);
                unset($productTags[$key]);
            }
        }

        $newProductTags = implode(',', $productTags ?: []);

        $maxBuyQty = isset($blackfridayData['max_buy_qty']) && is_numeric($blackfridayData['max_buy_qty']) ? $blackfridayData['max_buy_qty'] : 0;

        $productsIds = [$product->getId()];

        if($product->getTypeId() === GROUPED::TYPE_CODE){
            $childrenIds = $this->_groupedProductModel->getChildrenIds($product->getId());

            foreach ($childrenIds as $child){
                if(is_array($child)){
                    foreach ($child as $id){
                        array_push($productsIds, $id);
                    }
                }else{
                    array_push($productsIds, $child);
                }
            }
        }

        try {
            // Actualizamos atributos
            $this->_productAction->updateAttributes(
                $productsIds,
                [
                    'product_tags' => $newProductTags,
                    'blackfriday_max_buy_qty' => $maxBuyQty
                ],
                $store->getId()
            );

        } catch(\Exception $e) {
            $this->logBlackFriday($e->getMessage());
            return false;
        }

        return true;
    }

    protected function _fetchBlackFridayUpdateData($today)
    {
        try {
            $connection = $this->_resourceConnection->getConnection();
            $sql = "SELECT * FROM " . self::BLACKFRIDAY_BDTABLE . " WHERE from_date <= ? AND to_date >= ? AND processed = 0";
            return $connection->fetchAll($sql, array($today, $today));
        }catch (\Exception $e){
            $this->logBlackFriday("Error al extraer los blackfriday a actualizar de la tabla. Error ".$e->getMessage());
            return [];
        }
    }

    private function _fetchBlackFridayDeleteData($today){
        try{
            $connection = $this->_resourceConnection->getConnection();
            $sql        = "SELECT * FROM ".self::BLACKFRIDAY_BDTABLE."  WHERE to_date < ?";
            return $connection->fetchAll($sql, array($today));
        }catch (\Exception $e){
            $this->logBlackFriday("Error al extraer los blackfriday a borrar de la tabla. Error ".$e->getMessage());
            return [];
        }
    }

    protected function _deleteRecord($id)
    {
        $connection = $this->_resourceConnection->getConnection();
        $query = "DELETE FROM ".self::BLACKFRIDAY_BDTABLE." WHERE id = ?";
        $connection->query($query, [$id]);
    }

    protected function _markRecordAsProcessed($id, $processed = true)
    {
        $connection = $this->_resourceConnection->getConnection();
        if($processed) $query = "UPDATE ".self::BLACKFRIDAY_BDTABLE." SET processed=1, is_active = 1 WHERE id = ?";
        else $query = "UPDATE ".self::BLACKFRIDAY_BDTABLE." SET processed=1, is_active = 0 WHERE id = ?";
        $connection->query($query, [$id]);
    }

    protected function _getAttributeOptionId()
    {
        return $this->_blackFridayHelper->getBlackFridayTag();
    }

    private function logBlackFriday($message)
    {
        $this->logger->logUpdateBlackfriday($message);
    }

}
