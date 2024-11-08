<?php

namespace Hiperdino\Anniversary2020\Model\Participation;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Framework\Controller\Result\JsonFactory;

class ManagerRegisterParticipation
{
    const TYPE_REGISTER_RAFFLE = 'register_raffle';
    const TYPE_ASSOCIATED_CUSTOMER = 'associated_customer';
    const TYPE_GET_PARTICIPATION = 'get_participation';

    protected RaffleWebService $raffleWebService;
    protected Queue $participationQueue;
    protected Config $helperConfig;
    protected JsonFactory $resultJsonFactory;
    protected CustomerManager $customerManager;
    protected RegisterParticipation $registerParticipation;
    protected Logger $logger;
    protected Config $config;

    public function __construct(
        RaffleWebService $raffleWebService,
        Queue $participationQueue,
        Config $helperConfig,
        JsonFactory $resultJsonFactory,
        CustomerManager $customerManager,
        Logger $logger,
        RegisterParticipation $registerParticipation,
        Config $config
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->participationQueue = $participationQueue;
        $this->helperConfig = $helperConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerManager = $customerManager;
        $this->registerParticipation = $registerParticipation;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Registra la participación de un cliente.
     *
     * @param mixed $customer
     * @param string $rascaCode
     * @return void
     * @throws Exception
     */
    public function registerParticipation(mixed $customer, string $rascaCode)
    {
        $customAttribute = $customer->getCustomAttribute('sherpa_code');
        $customerId = $customer->getId();

        if (!preg_match('/^[A-Za-z0-9Ññ]+$/', $rascaCode)) {
            throw new Exception(__('Formato no válido.'));
        }

        $rascaCode = strtoupper($rascaCode);

        if ($customAttribute) {
            $customerSherpaCode = $customAttribute->getValue();
        } else {
            $errorMessage = 'Sherpa code not available for the customer';
            $this->logger->logRegisterParticipation(__($errorMessage));
            throw new Exception(__('Error al registrar el código de la participación'));
        }

        if ($this->config->isPromotionAvailable() & $this->config->isAnniversaryEnabled()) {
            $this->logger->logRegisterParticipation(__('Se comienza registro de participación de cliente : ' . $customerSherpaCode . ' Participacion: ' . $rascaCode));
            try {
                $participation = $this->validatedParticipation($rascaCode, $customer);
                $this->registerParticipation->registerRasca($customer, $rascaCode);

                $customerParticipation = $participation['salesClient'];

                if ($customerParticipation === null) {
                    $responseJson = $this->associatedCustomer($rascaCode, $customerSherpaCode);
                    $this->queueParticipation($responseJson, Queue::PROCESS, $rascaCode, self::TYPE_ASSOCIATED_CUSTOMER, '', $customerId);
                }

                try {
                    $data = json_encode(['participation_id' => $rascaCode]);
                    $responseJson = $this->associatedRaffle($rascaCode);
                    $this->queueParticipation($responseJson, Queue::PROCESS, $data, self::TYPE_REGISTER_RAFFLE, '', $customerId);
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $this->logger->logRegisterParticipation($message);
                    $this->queueParticipation($responseJson, Queue::ERROR, $rascaCode, self::TYPE_REGISTER_RAFFLE, $message, $customerId);
                    throw new Exception($e->getMessage());
                }

            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->queueParticipation(json_encode(["participations" => ['participation_id' => $rascaCode]]), Queue::ERROR, $rascaCode, self::TYPE_GET_PARTICIPATION, $message, $customerId);
                $this->logger->logRegisterParticipation($message);
                throw new Exception($message);
            }

        } else {
            throw new Exception($this->helperConfig->getMessageOutPromotion());
        }
    }

    private function queueParticipation($responseJson, $queueType, $rascaCode, $participationType, $errorMessage, $customerId)
    {
        $this->participationQueue->enqueue($responseJson, $queueType, $rascaCode, $participationType, $customerId, $errorMessage);
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
     * @return false|string
     * @throws Exception
     */
    public function associatedRaffle(string $rascaCode): string|false
    {
        $response = $this->raffleWebService->registerRaffle($rascaCode);

        return json_encode($response);
    }

    /**
     * @param string $rascaCode
     * @param mixed $customer
     * @return mixed
     * @throws Exception
     */
    public function validatedParticipation(string $rascaCode, mixed $customer)
    {
        $customerId = $customer->getId();
        $this->logger->logRegisterParticipation(__('Se comienza validación de participación: ' . $rascaCode));

        $participation = $this->raffleWebService->getParticipationById($rascaCode);

        $data = json_encode(['participation_id' => $rascaCode]);
        $this->participationQueue->enqueue(json_encode($participation), Queue::PROCESS, $data, self::TYPE_GET_PARTICIPATION, $customerId, '');

        $raffle = $participation['associatedWithLottery'];
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
        $customerId = $customer->getId();
        $this->customerManager->addNumWrongRascaRegistered($customer);

        $data = json_encode(["participations" => ['participation_id' => $rascaCode]]);
        $this->participationQueue->enqueue($data, Queue::ERROR, $rascaCode, self::TYPE_GET_PARTICIPATION, $customerId, $message);
        $this->logger->logRegisterParticipation($message);
    }
}
