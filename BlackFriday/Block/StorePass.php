<?php

namespace Hiperdino\BlackFriday\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Singular\Islands\Model\IslandsRepository;
use Singular\Tiendas\Model\TiendasFactory;
use Hiperdino\BlackFriday\Helper\StorePass as StorePassHelper;
use Magento\Customer\Model\Session as CustomerSession;

class StorePass extends Template
{

    protected $tiendasFactory;
    protected $islandsRepository;
    protected $storepassHelper;
    protected $customerSession;

    protected $tiendasCollection;
    protected $mediaUrl;

    public function __construct(
        CustomerSession $customerSession,
        StorePassHelper $storePassHelper,
        TiendasFactory $tiendasFactory,
        IslandsRepository $islandsRepository,
        ScopeConfigInterface $scopeConfig,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tiendasFactory = $tiendasFactory;
        $this->islandsRepository = $islandsRepository;
        $this->mediaUrl = $scopeConfig->getValue('web/secure/base_media_url') . 'tiendas/';
        $this->storepassHelper = $storePassHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIslands()
    {
        $islandArray = array();
        $islands = $this->tiendasFactory->create()->getCollection()
            ->addFieldToFilter('is_enabled', 1)
            ->addFieldToFilter('is_black_friday', 1)
            ->addFieldToSelect('island_id')
            ->getColumnValues('island_id');

        foreach($islands as $island) {
            $label = $this->islandsRepository->getById($island)->getName();
            $islandArray[$island] = [
                'value' => $island,
                'label' => $label,
            ];
        }

        return $islandArray;
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        $locationArray = array();
        $locations = $this->tiendasFactory->create()->getCollection()
            ->addFieldToFilter('is_enabled', 1)
            ->addFieldToFilter('is_black_friday', 1)
            ->addFieldToSelect('city')
            ->addFieldToSelect('island_id')
            ->setOrder('city','ASC');

        foreach($locations as $location) {
            $city = trim($location->getCity() ?: "");
            $locationArray[$city] = [
                'value' => $city,
                'island' => $location->getIslandId()
            ];
        }

        return $locationArray;
    }

    public function getTiendas()
    {
        if (!$this->tiendasCollection) {
            $island = $this->getRequest()->getParam('island', 0);
            $city = $this->getRequest()->getParam('city', 0);

            $stores = $this->tiendasFactory->create()->getCollection()->setOrder('name','ASC');
            $stores->addFieldToFilter('is_enabled', 1);
            $stores->addFieldToFilter('is_black_friday', 1);


            if ($island) {
                $stores->addFieldToFilter('island_id', $island);
                if ($city) $stores->addFieldToFilter('city', $city);
            }

            $this->tiendasCollection = $stores;
        }

        return $this->tiendasCollection;
    }

    public function getTiendaImage(\Singular\Tiendas\Model\Tiendas $tienda)
    {
        if ($tienda->getImage()) return $this->mediaUrl . $tienda->getImage();

        return false;
    }

    public function getTiendaAddress(\Singular\Tiendas\Model\Tiendas $tienda)
    {
        $address = $tienda->getAddress();
        if ($tienda->getPostcode()) $address .= ', ' . $tienda->getPostcode();
        if ($tienda->getCity()) $address .= ', ' . $tienda->getCity();
        if ($tienda->getProvince() && (strpos($address, $tienda->getProvince()) !== false)) $address .= ' - ' . $tienda->getProvince();

        return $address;
    }

    public function getAdditionalInfo(\Singular\Tiendas\Model\Tiendas $tienda)
    {
        $info = [];
        if ($tienda->getSpecialServices()) $info[] = $tienda->getSpecialServices();
        if ($tienda->getOtherServices()) $info[] = $tienda->getOtherServices();

        return $info;
    }

    public function isDefaultStore(\Singular\Tiendas\Model\Tiendas $tienda)
    {
        return $tienda->getStoreCode() == 'c9494';
    }

    public function getDefaultStore()
    {
        if (!isset($this->defaultStore)) {
            foreach ($this->getTiendas() as $tienda) {
                if ($this->isDefaultStore($tienda)) {
                    $this->defaultStore = $tienda;
                    break;
                }
            }
            if (!isset($this->defaultStore)) $this->defaultStore = false;
        }

        return $this->defaultStore;
    }

    /**
     * Devuelve la latitud por defecto para cuando no se selecciona ninguna provincia específica
     * @return bool|mixed
     */
    public function getDefaultLat()
    {
        if (($store = $this->getDefaultStore()) && ($store->getLatitude())) return $store->getLatitude();
        $lat = $this->_scopeConfig->getValue('tiendas/general/default_lat', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $lat ? $lat : false;
    }

    /**
     * Devuelve la longitud por defecto para cuando no se selecciona ninguna provincia específica
     * @return bool|mixed
     */
    public function getDefaultLon()
    {
        if (($store = $this->getDefaultStore()) && ($store->getLongitude())) return $store->getLongitude();
        $long = $this->_scopeConfig->getValue('tiendas/general/default_long', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $long ? $long : false;
    }

    /**
     * Devuelve la api de google maps del backend
     * @return mixed
     */
    public function getGoogleMapsKey()
    {
        return $this->_scopeConfig->getValue('tiendas/keys/maps', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param mixed $tienda
     * @return string
     */
    public function getBookingUrl($tienda)
    {
        return $this->getUrl('hiperdino_blackfriday/storepass/booking', ['t' => $tienda->getId()]);
    }

    /**
     * @return string
     */
    public function getBookingPostUrl()
    {
        return $this->getUrl('hiperdino_blackfriday/storepass/bookingPost');
    }

    /**
     * @return mixed
     */
    public function getCurrentShopId()
    {
        return $this->getRequest()->getParam('t', 0);
    }

    /**
     * @return array
     */
    public function getTimeslotsOptions()
    {
        $dates = [];
        $options = $this->storepassHelper->getTimeslotsCalendarByShopId($this->getCurrentShopId());
        foreach($options as $element) {
            if($element['available']) {
                $dates[$element['date']][] = $element;
            }
        }
        return $dates;
    }

    /**
     * @param $date
     * @return string
     */
    public function forDataHtml($date)
    {
        return str_replace('/', '', $date ?: "");
    }

    /**
     * @param $date
     * @return string
     */
    public function getDateDay($date)
    {
        return date('d', strtotime(str_replace('/', '-', $date ?: "")));
    }

    /**
     * @param $date
     * @return string
     */
    public function getDateFullLabel($date)
    {
        return $date;
    }

    /**
     * @return array|null
     */
    public function getCurrentBookingInfo()
    {
        $info = $this->storepassHelper->getCurrentBookingInfo($this->customerSession->getId());
        if(! $info) return null;
        /** @var \Hiperdino\BlackFriday\Model\StorepassTimeslot $timeslot */
        $timeslot = $info['timeslot'];
        /** @var \Singular\Tiendas\Model\Tiendas $tienda */
        $tienda = $this->tiendasFactory->create()->load($timeslot->getData('parent_store'));
        $info['shop'] = $tienda;
        return $info;
    }

    /**
     * @return string
     */
    public function getBookingCancelUrl()
    {
        return $this->getUrl('hiperdino_blackfriday/storepass/bookingCancel');
    }

    /**
     * @return string
     */
    public function getBookingModifyUrl()
    {
        return $this->getUrl('hiperdino_blackfriday/storepass/bookingModify');
    }

    /**
     * @param \Hiperdino\BlackFriday\Model\StorepassBooking $booking
     * @return string
     */
    public function getBookingQrImageUrl($booking)
    {
        return $this->getUrl('hiperdino_blackfriday/storepass/qr');
    }

    /**
     * @return \Singular\Tiendas\Model\Tiendas
     */
    public function getCurrentShop()
    {
        return $this->tiendasFactory->create()->load($this->getCurrentShopId());
    }

    /**
     * @return bool
     */
    public function isModifying()
    {
        return (bool) $this->customerSession->getData(StorePassHelper::SESSION_STOREPASS_IS_MODIFYING);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getIsModifyingMessage()
    {
        $linkToSuccess = $this->getUrl('hiperdino_blackfriday/storepass/success');
        return __('Recuerda que ya tienes <a href="%1">una reserva hecha</a> y te encuentras modificándola. Si confirmas la modificación, la reserva anterior quedará anulada.', $linkToSuccess);
    }
}
