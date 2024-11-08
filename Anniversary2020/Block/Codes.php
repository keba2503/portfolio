<?php

namespace Hiperdino\Anniversary2020\Block;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Participation\ManagerParticipationByCustomer;
use Hiperdino\Anniversary2020\Model\Participation\ManagerScratchParticipation;
use Hiperdino\Anniversary2020\Model\ResourceModel\Rasca\CollectionFactory;
use Hiperdino\Ux\Helper\FormKey;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\Element\Template;
use Singular\EcommerceApp\Helper\Data;

class Codes extends Template
{
    public const DATE_INTERNAL_FORMAT = 'd-m-Y';

    protected CollectionFactory $rascasCollection;
    protected Session $customerSession;
    protected Config $helperConfig;
    protected ManagerParticipationByCustomer $partcipationByCutomer;
    protected CustomerRepositoryInterface $customerRepository;
    protected ManagerScratchParticipation $scratchParticipation;
    protected FormKey $formKey;
    protected Data $dataApp;

    public function __construct(
        Template\Context $context,
        CollectionFactory $rascasCollection,
        ManagerParticipationByCustomer $partcipationByCutomer,
        Session $customerSession,
        Config $helperConfig,
        CustomerRepositoryInterface $customerRepository,
        ManagerScratchParticipation $scratchParticipation,
        FormKey $formKey,
        Data $dataApp,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->rascasCollection = $rascasCollection;
        $this->customerSession = $customerSession;
        $this->helperConfig = $helperConfig;
        $this->partcipationByCutomer = $partcipationByCutomer;
        $this->customerRepository = $customerRepository;
        $this->scratchParticipation = $scratchParticipation;
        $this->formKey = $formKey;
        $this->dataApp = $dataApp;
    }

    /**
     * @throws Exception
     */
    public function getCustomerCodes($scratch = true)
    {
        $participation = ['participations' => []];
        try {
            $customerId = $this->customerSession->getCustomerId();

            return $this->partcipationByCutomer->callParticipationByCustomer($customerId, $scratch);

        } catch (Exception) {
            return $participation;
        }
    }

    public function getTextOutPromo()
    {
        return $this->helperConfig->getRegisterRascaTextOutPromo();
    }

    public function isAnniversaryEnabled()
    {
        return $this->helperConfig->isAnniversaryEnabled();
    }

    public function getCustomerCodesRascasTable()
    {
        $rascasArray = [];
        try {
            $customerId = $this->customerSession->getCustomerId();
            $rascas = $this->rascasCollection->create();
            $rascas = $rascas->addFieldToFilter('customer_id', $customerId)->setOrder('date');
            $rascasArray = $rascas->getItems();
        } catch (Exception $e) {
        }

        return $rascasArray;
    }

    public function scratchParticipation($rascaCode)
    {
        $customer = $this->customerSession->getCustomerId();

        $this->scratchParticipation->scratchParticipation($customer, $rascaCode);
    }

    public function getWeekIdFromRasca($rascaId)
    {
        $rascasCollectionTable = $this->getCustomerCodesRascasTable();
        foreach ($rascasCollectionTable as $rasca) {
            if ($rasca['rasca_code'] == $rascaId) {
                return $rasca['week_id'];
            }
        }

        return null;
    }

    public function getDate($date)
    {
        return date("d/m/Y", strtotime($date ?: ""));
    }

    public function getWeekTitle($weekId)
    {
        return $this->helperConfig->getTitleWeeklyPromotion($weekId);
    }

    public function getFinalPromotionTitle()
    {
        return $this->helperConfig->getFinalPromotionTitle();
    }

    public function getAjaxActionUrl()
    {
        return $this->getUrl('hdanniversary/ajax/registerRasca', ['form_key' => $this->getFormKey()]);
    }

    public function getAjaxActionRasca()
    {
        return $this->getUrl('hdanniversary/ajax/ScratchRasca');
    }

    public function getScratchUrl()
    {
        return $this->getUrl('aniversario/rasca');
    }

    public function getFormKey()
    {
        return $this->formKey->getValue();
    }

    public function _getCurrentDateTime(): string
    {
        return (new \DateTime())->format(DateTime::DATE_INTERNAL_FORMAT);
    }

    public function getImageConfig()
    {
        return $this->helperConfig->getRegisterRascaImagePromo();
    }

    public function getTitleWeek()
    {
        return $this->helperConfig->getTitleWeeklyPromotion();
    }

    public function getFinalTitle()
    {
        return $this->helperConfig->getFinalPromotionTitle();
    }

    public function getCardNotRaffleText()
    {
        return $this->helperConfig->getTextCardNotRaffle();
    }

    public function getCardRaffleText()
    {
        return $this->helperConfig->getTextCardRaffle();
    }

    public function getButtonText()
    {
        return $this->helperConfig->getTextBoton();
    }

    public function getButtonScratchText()
    {
        return $this->helperConfig->getTextBotonScratch();
    }

    public function getCarrouselTitle()
    {
        return $this->helperConfig->getTextTitleCarrousel();
    }

    public function getCarrouselImage()
    {
        return $this->helperConfig->getImageCarrousel();
    }

    public function getModalPrizeText()
    {
        return __(str_replace(["#@", "@#"], "", $this->helperConfig->getTextModalPrize() ?: ""));
    }

    public function getBackgroundModalImg()
    {
        return $this->helperConfig->getBackgroundModal();
    }

    public function getImgWithoutPrize()
    {
        return $this->helperConfig->getImageWithoutPrize();
    }

    public function getIosStoreUrl()
    {
        return $this->dataApp->getIosDownloadUrl();
    }

    public function getAndroidStoreUrl()
    {
        return $this->dataApp->getAndroidDownloadUrl();
    }

    public function getScratchedPercentage()
    {
        return $this->helperConfig->getScratchedPercentage();
    }
}
