<?php

namespace Hiperdino\Anniversary2020\Model\Participation;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\ParticipationWinnerEmail;
use Hiperdino\Anniversary2020\Model\Service\Qr;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Hiperdino\Coupons\Api\RedemptionManagementInterface;
use Hiperdino\Homai\Helper\Coupon;
use Hiperdino\Sales\Model\OrderByIncrement;

class ManagerScratchParticipation
{
    const TYPE_SCRATCH_PARTICIPATION = 'scratch_participation';
    const TYPE_GET_PARTICIPATION = 'get_participation';
    const WEB = '1';

    protected RaffleWebService $raffleWebService;
    protected Queue $participationQueue;
    protected Config $helperConfig;
    protected CustomerManager $customerManager;
    protected Coupon $coupon;
    protected Logger $logger;
    protected ParticipationWinnerEmail $email;
    protected OrderByIncrement $orderByIncrement;
    protected ManagerParticipationByCustomer $participationByCustomer;
    protected Qr $qr;
    protected RedemptionManagementInterface $redemptionManagement;

    public function __construct(
        RaffleWebService $raffleWebService,
        Queue $participationQueue,
        Config $helperConfig,
        CustomerManager $customerManager,
        Logger $logger,
        Coupon $coupon,
        ParticipationWinnerEmail $email,
        OrderByIncrement $orderByIncrement,
        ManagerParticipationByCustomer $participationByCustomer,
        Qr $qr,
        RedemptionManagementInterface $redemptionManagement
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->participationQueue = $participationQueue;
        $this->helperConfig = $helperConfig;
        $this->customerManager = $customerManager;
        $this->coupon = $coupon;
        $this->logger = $logger;
        $this->email = $email;
        $this->orderByIncrement = $orderByIncrement;
        $this->participationByCustomer = $participationByCustomer;
        $this->qr = $qr;
        $this->redemptionManagement = $redemptionManagement;
    }

    /**
     * Registra la participación de un cliente.
     *
     * @param mixed $customer
     * @param string $rascaCode
     * @return void
     * @throws Exception
     */
    public function scratchParticipation(mixed $customer, string $rascaCode)
    {
        $this->logger->logParticipationScratch(__('Se comienza proceso de rascado de participación: ' . $rascaCode));

        $customerId = $customer->getId();;

        try {
            $this->validatedParticipation($rascaCode, $customer);

            if ($this->helperConfig->isScratchAvailable()) {
                $responseJson = $this->scratch($rascaCode);
                $data = json_encode(['participation_id' => $rascaCode]);
                $this->queueParticipation($responseJson, Queue::PROCESS, $data, self::TYPE_SCRATCH_PARTICIPATION, '', $customerId);
            } else {
                throw new Exception($this->helperConfig->getMessageOutPromotion());
            }

        } catch (Exception $e) {
            $responseJson = json_encode(["participations" => ['participation_id' => $rascaCode]]);
            $message = $e->getMessage();
            $this->queueParticipation($responseJson, Queue::ERROR, $rascaCode, self::TYPE_SCRATCH_PARTICIPATION, $message, $customerId);
            $this->logger->logParticipationScratch($message);
            throw new Exception($e->getMessage());
        }
    }

    private function queueParticipation($responseJson, $queueType, $rascaCode, $participationType, $errorMessage, $customerId)
    {
        $this->participationQueue->enqueue($responseJson, $queueType, $rascaCode, $participationType, $customerId, $errorMessage);
    }

    /**
     * @param string $rascaCode
     * @return false|string
     * @throws Exception
     */
    public function scratch(string $rascaCode): string|false
    {
        $response = $this->raffleWebService->scratchParticipation($rascaCode);

        return json_encode($response);
    }

    /**
     * @param string $rascaCode
     * @param mixed $customer
     * @return void
     * @throws Exception
     */
    public function validatedParticipation(string $rascaCode, mixed $customer): void
    {
        $participation = $this->raffleWebService->getParticipationById($rascaCode);
        $customerId = $customer->getId();

        if (!$participation) {
            $message = $this->helperConfig->getErrorInvalidRasca();
            $this->customerManager->addNumWrongRascaRegistered($customer);
            $data = json_encode(['participation_id' => $rascaCode]);
            $this->participationQueue->enqueue(json_encode($participation), Queue::ERROR, $data, self::TYPE_GET_PARTICIPATION, $customerId, $message);

            $this->logger->logParticipationScratch($message);

            throw new Exception($message);
        }

        if ($participation['couponCode'] !== null) {
            $this->associateAndActivateCoupon($participation['couponCode'], $customer);
            $couponInfo = $this->participationByCustomer->getCouponInfo($participation["couponCode"]);
            $couponEndDate = $couponInfo['endDate'];
            $couponName = $couponInfo['couponName'];
            $this->orderPlatform($participation['salesId'], $customer, $participation['couponCode'], $couponName, $couponEndDate);
        }
    }

    /**
     * @param mixed $salesId
     * @param $customer
     * @param $couponCode
     * @param $couponName
     * @param $couponEndDate
     * @return void
     */
    public function orderPlatform(mixed $salesId, $customer, $couponCode, $couponName, $couponEndDate): void
    {
        try {
            $order = $this->orderByIncrement->getOrderIdByIncrementId($salesId);

            if ($order) {
                $orderData = $order->getData();
                $platform = $orderData['platform'];

                if ($platform === self::WEB) {
                    $filename = $this->qr->generateQR($couponCode);
                    $this->logger->logRequestParticipation(__('** Se genera el siguiente codigo QR ** :' . $filename));
                    $this->email->sendEmail($customer, $couponCode, $couponName, $couponEndDate, $filename);
                    $this->logger->logRequestParticipation(__('** Cliente web, se genera envio de email con cupón ** :' . $couponCode));

                } else {
                    $this->logger->logParticipationScratch(__('La participación no pertenece a un cliente WEB'));
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->logParticipationScratch($message);
        }
    }

    /**
     * @param string $couponCode
     * @param mixed $customer
     * @return void
     * @throws Exception
     */
    public function associateAndActivateCoupon(string $couponCode, mixed $customer)
    {
        try {
            $customerId = $customer->getId();
            $this->coupon->addCoupon($couponCode, $customerId);
            $this->logger->logParticipationScratch(__('Se asocia cupón'));

            if ($this->redemptionManagement->isActivatable($this->coupon->getCoupon($couponCode), $customerId)) {
                $this->coupon->updateCouponManualSelection($couponCode, true);
            }

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logParticipationScratch($message);
            throw new Exception($message);
        }
    }
}
