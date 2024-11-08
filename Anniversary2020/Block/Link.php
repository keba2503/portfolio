<?php

namespace Hiperdino\Anniversary2020\Block;

use Hiperdino\Anniversary2020\Helper\Config as AnniversaryHelper;
use Hiperdino\BlackFriday\Helper\BlackFriday2020 as BlackFridayHelper;
use Hiperdino\BP\Helper\Data;
use Hiperdino\Cache\Model\Manager;
use Hiperdino\Sopladera\Helper\Data as SopladeraHelper;
use Hiperdino\Ux\Block\Sidemenu;
use Hiperdino\Ux\Helper\Config;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Link extends Sidemenu
{
    protected AnniversaryHelper $anniversaryConfigHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        Registry $registry,
        Session $customerSession,
        Data $bpHelper,
        Manager $hiperdinoCache,
        SopladeraHelper $sopladeraHelper,
        BlackFridayHelper $blackFridayHelper,
        Config $uxConfigHelper,
        AnniversaryHelper $anniversaryConfigHelper,
        array $data = []
    ) {
        parent::__construct($context, $categoryCollectionFactory, $registry, $customerSession, $bpHelper, $hiperdinoCache, $sopladeraHelper, $blackFridayHelper, $uxConfigHelper, $data);
        $this->anniversaryConfigHelper = $anniversaryConfigHelper;
    }

    public function isAnniversaryEnabled()
    {
        return $this->anniversaryConfigHelper->isAnniversaryEnabled();
    }

    public function getAnniversaryUrl()
    {
        return $this->getUrl($this->anniversaryConfigHelper->getUrlCmsAnniversary());
    }

    public function getMenuCustomerTitle()
    {
        return $this->anniversaryConfigHelper->getRascaMenuCustomer();
    }

    public function getAnniversary2020TitleMenu()
    {
        return $this->anniversaryConfigHelper->getTitleMenu();
    }

    public function getUrlAnniversary2020InsertYourCode()
    {
        $page = $this->anniversaryConfigHelper->getUrlCmsRasca();

        return $this->getUrl($page);
    }

    public function getUrlAnniversary2020Raffles()
    {
        $page = $this->anniversaryConfigHelper->getUrlCmsRaffles();

        return $this->getUrl($page);
    }

    public function getUrlAnniversary2020DirectAwards()
    {
        $page = $this->anniversaryConfigHelper->getUrlCmsDirectAwards();

        return $this->getUrl($page);
    }

    public function getUrlAnniversary2020Participate()
    {
        $page = $this->anniversaryConfigHelper->getUrlCmsParticipate();

        return $this->getUrl($page);
    }

    public function getUrlAnniversary2020Legal()
    {
        $page = $this->anniversaryConfigHelper->getUrlCmsLegal();

        return $this->getUrl($page);
    }

    public function getUrlAnniversary2020SeeCodes()
    {
        return $this->getUrl('hdanniversary/codes');
    }

    public function getRegisterTitle()
    {
        return $this->anniversaryConfigHelper->getTitleRegisterParticipation();
    }
}
