<?php

namespace Hiperdino\Anniversary2020\Model\Customer;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class CustomerManager
{
    const ANNIVERSARY2020_BDTABLE = "hiperdino_anniversary2020_rascas";
    const NUM_MAX_WRONG_RASCAS_REGISTED = 20;

    protected Config $helperConfig;
    protected ResourceConnection $resourceConnection;
    protected CustomerRepositoryInterface $customerRepository;
    protected Logger $log;
    protected CollectionFactory $raffleRgpdFactory;

    public function __construct(
        Config $helperConfig,
        ResourceConnection $resourceConnection,
        CustomerRepositoryInterface $customerRepository,
        CollectionFactory $raffleRgpdFactory,
        Logger $log
    ) {
        $this->helperConfig = $helperConfig;
        $this->resourceConnection = $resourceConnection;
        $this->customerRepository = $customerRepository;
        $this->raffleRgpdFactory = $raffleRgpdFactory;
        $this->log = $log;
    }

    public function customerCanRegisterOtherRasca($customer)
    {
        $numMax = $this->getNumMaxWrongRascasRegistered();
        $num = $this->getNumWrongRascasRegistered($customer);

        return $num < $numMax;
    }

    /**
     * @return bool
     */
    public function customerCanParticipate($customer)
    {
        $currentYear = date('Y');

        $customerId = $customer->getId();
        $raffleRgpdCollection = $this->raffleRgpdFactory->create();
        $raffleRgpd = $raffleRgpdCollection
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('created_at', array(
                'gteq' => $currentYear . '-01-01 00:00:00',
                'lteq' => $currentYear . '-12-31 23:59:59',
            ))
            ->getFirstItem();

        $dni = (string)$raffleRgpd->getTaxvat();
        $customerTelephone = (string)$raffleRgpd->getPhone();
        $customerIsland = (int)$raffleRgpd->getIsland();
        $acceptAnniversary = (int)$raffleRgpd->getAcceptRgpd();

        return $acceptAnniversary && $dni && $customerTelephone && $customerIsland;
    }

    public function setCustomerTelephone($customer, $newTelephone)
    {
        $this->log->Log("Entra en setCustomerTelephone");
        $customer->setCustomAttribute('customer_telephone', $newTelephone);
        $this->customerRepository->save($customer);
    }

    public function addNumWrongRascaRegistered($customer)
    {
        $num = $this->getNumWrongRascasRegistered($customer);
        $this->setNumWrongRascasRegistered($customer, ($num + 1));
    }

    /**
     * @param mixed $customerId
     * @return string
     */
    public function getCustomerRascasHistoryMsg(mixed $customerId)
    {
        if (!$this->helperConfig->isAnniversaryEnabled() || !$this->helperConfig->getActiveWeek()) {
            return '';
        }

        try {
            $connection = $this->resourceConnection->getConnection();
            $sql = "SELECT COUNT(*) AS total FROM " . self::ANNIVERSARY2020_BDTABLE . " WHERE customer_id = ? AND week_id = ?";
            $total = $connection->fetchOne($sql, [$customerId, $this->helperConfig->getActiveWeek()]);

            if (!$total) {
                return '';
            }

            return __("Hasta ahora has registrado un total de %1 códigos.", $total, $total == 1 ? '' : 's');
        } catch (Exception $e) {
            return '';
        }
    }

    public function getNumWrongRascasRegistered($customer)
    {
        $num = $customer->getCustomAttribute('num_wrong_rascas_registered');
        if (!$num) {
            $num = 0;
            $this->setNumWrongRascasRegistered($customer, $num);
        } else {
            $num = $num->getValue();
        }

        return $num;
    }

    public function setNumWrongRascasRegistered($customer, $qty)
    {
        $customer->setCustomAttribute('num_wrong_rascas_registered', $qty);
        $this->customerRepository->save($customer);
    }

    public function getNumMaxWrongRascasRegistered()
    {
        $numMax = $this->helperConfig->getScopeRegisterRascaValue();
        if (!$numMax || !is_numeric($numMax)) {
            $numMax = self::NUM_MAX_WRONG_RASCAS_REGISTED;
        }

        return $numMax;
    }
}
