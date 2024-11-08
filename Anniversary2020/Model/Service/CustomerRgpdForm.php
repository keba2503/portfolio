<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Exception;
use Hiperdino\Anniversary2020\Api\RaffleRgpdRepositoryInterface;
use Hiperdino\Anniversary2020\Model\RaffleRgpdFactory;
use Hiperdino\Anniversary2020\Helper\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRgpdForm
{
    protected CustomerRepositoryInterface $customerRepository;
    protected Logger $logger;
    protected RaffleRgpdRepositoryInterface $raffleRgpdRepository;
    protected RaffleRgpdFactory $raffleRgpdFactory;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RaffleRgpdRepositoryInterface $raffleRgpdRepository,
        RaffleRgpdFactory $raffleRgpdFactory,
        Logger $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->raffleRgpdRepository = $raffleRgpdRepository;
        $this->raffleRgpdFactory = $raffleRgpdFactory;
    }

    /**
     * @param mixed $dni
     * @param mixed $customerTelephone
     * @param mixed $customerIsland
     * @param int $customerId
     * @return CustomerInterface
     * @throws Exception
     */
    public function customerRgpdForm($dni, $customerTelephone, $customerIsland, $customerId): CustomerInterface
    {
        try {
            $currentDateTime = date('Y-m-d H:i:s');
            $customerData = $this->customerRepository->getById($customerId);
            $customerDni = strtoupper($dni);

            $raffleRgpd = $this->raffleRgpdFactory->create();
            $raffleRgpd->setCustomerId($customerData->getId());
            $raffleRgpd->setTaxvat($customerDni);
            $raffleRgpd->setPhone($customerTelephone);
            $raffleRgpd->setIsland($customerIsland);
            $raffleRgpd->setCreatedAt($currentDateTime);
            $raffleRgpd->setAcceptRgpd(1);

            $this->raffleRgpdRepository->save($raffleRgpd);

            return $customerData;
        } catch (Exception $e) {
            $this->logger->logRegisterCustomeRgpd('Log customerRgpdForm: ' . $e->getMessage());
            throw $e;
        }
    }
}

