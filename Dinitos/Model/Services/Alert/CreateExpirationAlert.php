<?php

namespace Hiperdino\Dinitos\Model\Services\Alert;

use Exception;
use Hiperdino\Alert\Helper\Alerts;
use Hiperdino\Alert\Model\AlertType;
use Hiperdino\Alert\Model\AlertTypeRepository;
use Hiperdino\Alert\Model\Data\AlertRequest;
use Hiperdino\Alert\Model\Data\AlertRequestFactory;
use Hiperdino\Dinitos\Helper\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CreateExpirationAlert
{
    const DINITOS_EXPIRATION_VALUE = 'dinitos_expiration';
    const GENERAL_ALERT_CODE = 'general';

    public function __construct(
        protected AlertRequestFactory $alertRequestFactory,
        protected Alerts $alertHelper,
        protected AlertTypeRepository $alertTypeRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected Config $config,
        protected $alertType = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function createAlert($package): bool
    {
        try {
            $expirationAlert = $this->buildAlert($package);
            $this->alertHelper->createAlert($expirationAlert);

        } catch (Exception) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function buildAlert($package): AlertRequest
    {
        try {
            $this->getAlertType();
            $expirationDateFormatted = $this->formatExpirationDate($package->getExpirationDate());
            $alertRequest = $this->alertRequestFactory->create();
            $alertRequest->setType($this->alertType->getCode());
            $alertRequest->setCustomerCode($package->getCustomerId());
            $alertRequest->setTitle($this->config->getReminderAlertTitle() ?: $this->alertType->getTitle());
            $alertRequest->setMessage($this->config->getReminderPushText());
            $alertRequest->setExtraMessage("El {$expirationDateFormatted} caducarán {$package->getAvailableDinitos()} de tus dinitos");
            $alertRequest->setPusheable(1);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $alertRequest;
    }

    private function getAlertType(): void
    {
        $this->searchCriteriaBuilder->addFilter('code', self::DINITOS_EXPIRATION_VALUE, 'like');
        $alertType = $this->alertTypeRepository->getList($this->searchCriteriaBuilder->create())->getItems()[0];
        /* @var AlertType $alertType */
        if (!$alertType->getId()) {
            $alertType = $this->alertTypeRepository->getByCode(self::GENERAL_ALERT_CODE);
        }

        $this->alertType = $alertType;
    }

    private function formatExpirationDate($expirationDate)
    {
        $dateArray = explode(' ', $expirationDate);
        $dateArray[0] = implode('-', array_reverse(explode('-', $dateArray[0])));

        return implode(' ', $dateArray);
    }
}