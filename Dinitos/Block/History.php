<?php

namespace Hiperdino\Dinitos\Block;

use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetMovementsCustomer;
use Hiperdino\Dinitos\Model\Services\Package\GetToExpire;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class History extends Template
{
    public function __construct(
        protected Config $config,
        protected Session $customerSession,
        protected GetMovementsCustomer $historyByCustomer,
        protected GetToExpire $packageToExpire,
        protected GetDinitos $customerDinitos,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isDinitosLinkEnabled()
    {
        return $this->config->isDinitosAccumulatedEnabledWeb();
    }

    public function getDinitosHistorySummary()
    {
        $customerId = $this->customerSession->getCustomerId();

        return $this->historyByCustomer->callHistoryByCustomer($customerId);
    }

    public function getDinitosBalance()
    {
        $customerId = $this->customerSession->getCustomerId();

        return $this->customerDinitos->getCustomerDinitosTotal($customerId);
    }

    public function dateShortener($date = "")
    {
        return substr($date, 0, strpos($date, " "));
    }

    public function humaniseDate($date = "")
    {
        if ($date === '') {
            return '';
        }
        $shortDate = substr($date, 0, strpos($date, " "));
        $dateParts = explode('-', $shortDate);
        $year = $dateParts[0];
        $month = $dateParts[1];
        $day = $dateParts[2];

        return $day . '-' . $month . '-' . $year;
    }

    public function prependMinusSymbol($transactionType = '')
    {
        $map = ['0' => '', '1' => '-', '2' => '', '3' => '', '4' => '-'];

        return $map[$transactionType] ?? '';
    }

    public function applyTrasantionCSS($transactionType = "")
    {
        $map = ['0' => 'accumulated', '1' => 'redeemed', '2' => 'expired', '3' => 'refunded', '4' => 'deducted'];

        return $map[$transactionType] ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDinitosHistoryTexts()
    {
        return $this->config->getTextHistory();
    }

    public function getNextExpirablePackage()
    {
        $customerId = $this->customerSession->getCustomerId();
        $nextExpirablePackage = $this->packageToExpire->getNextExpirablePackage($customerId);

        if (isset($nextExpirablePackage['dinitos_quantity'], $nextExpirablePackage['expiration_date'])) {
            return [
                'dinitos_quantity' => $nextExpirablePackage['available_dinitos'],
                'expiration_date' => $nextExpirablePackage['expiration_date']
            ];
        }

        return [];
    }

    public function getNextExpirableQty()
    {
        $nextExpirablePackage = $this->getNextExpirablePackage();

        if (!empty($nextExpirablePackage) && isset($nextExpirablePackage['dinitos_quantity'])) {
            return $nextExpirablePackage['dinitos_quantity'];
        }

        return '';
    }

    public function getNextExpirableDate()
    {
        $nextExpirablePackage = $this->getNextExpirablePackage();

        if (!empty($nextExpirablePackage) && isset($nextExpirablePackage['expiration_date'])) {
            return $nextExpirablePackage['expiration_date'];
        }

        return '';
    }

    public function getHistoryFilters()
    {
        return $this->config->getFilterHistory();
    }
}