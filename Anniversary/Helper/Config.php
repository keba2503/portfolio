<?php

namespace Hiperdino\Anniversary\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use DateTime;
use DateTimeZone;

class Config
{
    const CODE = "anniversary";
    const RASCAS_QTY_BY_X_EUROS = "hiperdino_anniversary/general/rascas";
    const EUROS_BY_Y_RASCAS = "hiperdino_anniversary/general/euros";
    const MAX_RASCAS_EXTRA = "hiperdino_anniversary/general/rascas_extra";
    const PRODUCT_NUM_FOR_EXTRA = "hiperdino_anniversary/general/numero_productos";
    const PROMO_END = "hiperdino_anniversary/general/promo_end";
    const ENABLED = "hiperdino_anniversary/general/enabled";
    const PROMO_START = "hiperdino_anniversary/general/promo_start";
    const RASCA_TAG = "hiperdino_anniversary/general/rasca_tag";
    const RASCA_CSV_EXPORT_SALES = "hiperdino_anniversary/general/rasca_label_csv_export_sales";
    const RASCA_TIME_LABEL_SINGLE = 'hiperdino_anniversary/general/times_label_single';
    const RASCA_TIME_LABEL_PLURAL = 'hiperdino_anniversary/general/times_label_plural';
    const RASCA_EXTRA_LABEL_SINGLE = 'hiperdino_anniversary/general/extra_label_single';
    const RASCA_EXTRA_LABEL_PLURAL = 'hiperdino_anniversary/general/extra_label_plural';
    const SHOW_IN_ORDER_CSV = "hiperdino_anniversary/general/show_in_order_csv";
    const RASCA_LABEL = "hiperdino_anniversary/general/rasca_label";

    protected StoreManagerInterface $storeManager;
    protected ScopeConfigInterface $scopeConfig;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getPromoStart()
    {
        return $this->getValue(self::PROMO_START);
    }

    public function getPromoEnd()
    {
        return $this->getValue(self::PROMO_END);
    }

    public function getEnabled()
    {
        return $this->getValue(self::ENABLED);
    }

    public function promotionAvailable()
    {
        $timezone = new DateTimeZone('Atlantic/Canary');
        $today = new DateTime('now', $timezone);
        $fromDate = new DateTime($this->getPromoStart() . " 00:00:00");
        $toDate = new DateTime($this->getPromoEnd() . " 22:59:59");
        $enabled = $this->getEnabled();

        if ($enabled && $fromDate <= $today && $toDate >= $today) {
            return true;
        }

        return false;
    }

    public function getRascasQtyByEuros()
    {
        $value = $this->getValue(self::RASCAS_QTY_BY_X_EUROS);

        return intval($value) ?: 0;
    }

    public function getEurosByRascas()
    {
        $value = $this->getValue(self::EUROS_BY_Y_RASCAS);

        return intval($value) ?: 0;
    }

    public function getMaxRascasExtra()
    {
        $value = $this->getValue(self::MAX_RASCAS_EXTRA);

        return intval($value) ?: 0;
    }

    public function getProductNumForExtra()
    {
        $value = $this->getValue(self::PRODUCT_NUM_FOR_EXTRA);

        return intval($value) ?: 0;
    }

    public function getRascaTag()
    {
        return $this->getValue(self::RASCA_TAG);
    }

    public function getRascaCsvExportSales()
    {
        return $this->getValue(self::RASCA_CSV_EXPORT_SALES);
    }

    public function getRascaTimeLabelSingle()
    {
        return $this->getValue(self::RASCA_TIME_LABEL_SINGLE) ?: '';
    }

    public function getRascaTimeLabelPlural()
    {
        return $this->getValue(self::RASCA_TIME_LABEL_PLURAL) ?: '';
    }

    public function getRascaExtraLabelSingle()
    {
        return $this->getValue(self::RASCA_EXTRA_LABEL_SINGLE) ?: '';
    }

    public function getRascaExtraLabelPlural()
    {
        return $this->getValue(self::RASCA_EXTRA_LABEL_PLURAL) ?: '';
    }

    public function getShowInOrderCsv()
    {
        return $this->getValue(self::SHOW_IN_ORDER_CSV);
    }

    public function getRascaLabel()
    {
        if ($this->getValue(self::RASCA_LABEL)) {
            return $this->getValue(self::RASCA_LABEL);
        } else {
            return "Aniversario";
        }
    }

    private function getValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeManager->getStore());
    }
}
