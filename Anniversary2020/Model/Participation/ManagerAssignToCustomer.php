<?php

namespace Hiperdino\Anniversary2020\Model\Participation;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Hiperdino\DigitalTicket\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\ManagerInterface;

class ManagerAssignToCustomer
{
    const TYPE_ASSOCIATED_CUSTOMER = 'associated_customer';
    const TYPE_GET_PARTICIPATION = 'get_participation';

    protected RaffleWebService $raffleWebService;
    protected Queue $participationQueue;
    protected Config $helperConfig;
    protected CustomerManager $customerManager;
    protected RegisterParticipation $registerParticipation;
    protected Logger $logger;
    protected CustomerRepositoryInterface $customerRepository;
    protected Data $digitalTicketHelper;
    protected ManagerInterface $eventManager;

    public function __construct(
        RaffleWebService $raffleWebService,
        Queue $participationQueue,
        Config $helperConfig,
        CustomerManager $customerManager,
        Logger $logger,
        RegisterParticipation $registerParticipation,
        CustomerRepositoryInterface $customerRepository,
        Data $digitalTicketHelper,
        ManagerInterface $eventManager,
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->participationQueue = $participationQueue;
        $this->helperConfig = $helperConfig;
        $this->customerManager = $customerManager;
        $this->registerParticipation = $registerParticipation;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->digitalTicketHelper = $digitalTicketHelper;
        $this->eventManager = $eventManager;
    }

    public function assignToCustomer($digitalTicket)
    {
        try {
            $ticketInfo = $digitalTicket->getData();

            $customerId = $ticketInfo['customer_id'];
            $customer = $this->customerRepository->getById($customerId);
            $customAttribute = $customer->getCustomAttribute('sherpa_code');

            if (!$customAttribute) {
                $mesage = 'Sherpa code not available for the customer';
                $this->logger->logAssignParticipationByCustomer($mesage);
                throw new Exception($mesage);
            }

            $customerSherpaCode = $customAttribute->getValue();

            $digitalTicketId = $ticketInfo['entity_id'];
            $digitalTicket = $this->digitalTicketHelper->getDigitalTicketDetailById($digitalTicketId);

            $participation = $digitalTicket->getParticipations();

            $this->logger->logAssignParticipationByCustomer(__('Se comienza proceso de asociar participación a cliente: ' .$customerSherpaCode));
            $this->associatedParticipation($participation, $customer, $customerSherpaCode);

            $this->eventManager->dispatch("customer_obtained_participation", ["customer_id" => $customerId]);

        } catch (Exception $e) {
            $this->logger->logAssignParticipationByCustomer($e->getMessage());
        }
    }

    /**
     * @param array $participation
     * @param CustomerInterface $customer
     * @param mixed $customerSherpaCode
     * @return void
     * @throws Exception
     */
    public function associatedParticipation(array $participation, CustomerInterface $customer, mixed $customerSherpaCode): void
    {
        $customerId = $customer->getId();

        if (empty($participation)) {
            $mesage = 'Invalid participation data';
            $this->logger->logAssignParticipationByCustomer($mesage);
            throw new Exception($mesage);
        }

        foreach ($participation as $participationInfo) {
            $participationData = $participationInfo->getData();

            if (!isset($participationData['code'])) {
                $mesage = 'Invalid participation data';
                $this->logger->logAssignParticipationByCustomer($mesage);
                throw new Exception($mesage);
            }

            $rascaCode = $participationData['code'];

            try {
                $this->validateParticipation($rascaCode, $customer);
            } catch (Exception $e) {
                $this->logger->logAssignParticipationByCustomer($e->getMessage());
                continue;
            }

            $responseJson = $this->associatedCustomer($rascaCode, $customerSherpaCode);
            $this->logger->logAssignParticipationByCustomer($responseJson);
            $this->queueParticipation($responseJson, Queue::PROCESS, $rascaCode, self::TYPE_ASSOCIATED_CUSTOMER, '', $customerId);

        }
    }


    private function queueParticipation($responseJson, $queueType, $rascaCode, $participationType, $customerId, $errorMessage)
    {
        $this->participationQueue->enqueue($responseJson, $queueType, $rascaCode, $participationType, $errorMessage, $customerId);
    }

    /**
     * @param string $rascaCode
     * @param mixed $customerSherpaCode
     * @return false|string
     * @throws Exception
     */
    public function associatedCustomer(string $rascaCode, mixed $customerSherpaCode): string|false
    {
        $response = $this->raffleWebService->associatedCustomerParticipation($rascaCode, $customerSherpaCode);

        return json_encode($response);
    }

    /**
     * @param string $rascaCode
     * @param mixed $customer
     * @return mixed
     * @throws Exception
     */
    public function validateParticipation(string $rascaCode, mixed $customer)
    {
        $customerId = $customer->getId();

        $participation = $this->raffleWebService->getParticipationById($rascaCode);
        $raffle = $participation['associatedWithLottery'];
        $data = json_encode(["participations" => ['participation_id' => $rascaCode]]);

        $this->participationQueue->enqueue($data, Queue::PROCESS, $rascaCode, self::TYPE_GET_PARTICIPATION, $customerId, '' );

        $scratch = ($participation["scratched"] === false) ? false : true;

        if ($raffle || !$participation || !$scratch) {
            if ($raffle) {
                $message = 'El código indicado ya está registrado en un sorteo';
            } elseif (!$participation) {
                $message = $this->helperConfig->getErrorInvalidRasca();
            } else {
                $message = 'La participación aún no ha sido rascada';
            }
            $this->handleFailedParticipation($rascaCode, $customer, $message);
            throw new Exception($message);
        }

        return $participation;
    }

    /**
     * Handle the logic for failed participation
     *
     * @param string $rascaCode
     * @param mixed $customer
     * @param string $message
     * @return void
     */
    private function handleFailedParticipation(string $rascaCode, mixed $customer, string $message): void
    {
        $this->customerManager->addNumWrongRascaRegistered($customer);
        $customerId = $customer->getId();

        $data = json_encode(["participations" => ['participation_id' => $rascaCode]]);
        $this->participationQueue->enqueue($data, Queue::ERROR, $rascaCode, self::TYPE_GET_PARTICIPATION, $customerId, $message);
        $this->logger->logAssignParticipationByCustomer($message);
    }
}
