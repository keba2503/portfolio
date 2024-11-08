<?php

namespace Hiperdino\Anniversary2020\Model\Participation;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config as ConfigAnniversary;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Anniversary2020\Model\ResourceModel\Rasca\CollectionFactory;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Hiperdino\Coupons\Model\RedemptionRepository;
use Hiperdino\Homai\Helper\Config;
use Hiperdino\Homai\Helper\Coupon;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ManagerParticipationByCustomer
{
    const GET_PARTICIPATION_CUSTOMER = 'participation_by_customer';

    protected RaffleWebService $raffleWebService;
    protected Logger $logger;
    protected Queue $participationQueue;
    protected Coupon $coupon;
    protected Config $homaiConfig;
    protected RedemptionRepository $redemptionRepository;
    protected ConfigAnniversary $configAnniversary;
    protected CustomerRepositoryInterface $customerRepository;
    protected CollectionFactory $rascasCollection;

    public function __construct(
        RaffleWebService $raffleWebService,
        Logger $logger,
        Queue $participationQueue,
        Coupon $coupon,
        Config $homaiConfig,
        ConfigAnniversary $configAnniversary,
        RedemptionRepository $redemptionRepository,
        CustomerRepositoryInterface $customerRepository,
        CollectionFactory $rascasCollection,
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->logger = $logger;
        $this->participationQueue = $participationQueue;
        $this->coupon = $coupon;
        $this->homaiConfig = $homaiConfig;
        $this->redemptionRepository = $redemptionRepository;
        $this->configAnniversary = $configAnniversary;
        $this->customerRepository = $customerRepository;
        $this->rascasCollection = $rascasCollection;
    }

    /**
     * Retrieves participations for a specific customer and filters them based on scratch.
     *
     * @param string $customerId
     * @param bool $scratch Determines if participations should be filtered by scratch.
     * @return array The filtered participations.
     * @throws Exception If an error occurs during the process.
     */
    public function callParticipationByCustomer(string $customerId, bool $scratch): array
    {
        $customer = $this->customerRepository->getById($customerId);
        $customerSherpaCode = $customer->getCustomAttribute('sherpa_code');
        if ($customerSherpaCode) {
            $customerSherpaCode = $customerSherpaCode->getValue();
            $this->logger->logParticipationByCustomer(__('Se comienza consulta de participaciones de cliente : ' . $customerSherpaCode));
            if ($this->configAnniversary->isAnniversaryEnabled()) {
                $response = $this->raffleWebService->getParticipationByCustomer($customerSherpaCode);
                $response = $this->orderParticipation($response);

                $mappedParticipations = $this->normalizeParticipations($response["participations"], $customerId);
                $filteredParticipations = $this->filterParticipationsByScratch($mappedParticipations, $scratch);

                $responseJson = json_encode(["participations" => $filteredParticipations]);
            } else {
                throw new Exception($this->configAnniversary->getMessageOutPromotion());
            }

            try {

                $this->participationQueue->enqueue($responseJson, Queue::PROCESS, $customerSherpaCode, self::GET_PARTICIPATION_CUSTOMER, $customerId, '');
                return $filteredParticipations;

            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                $this->logger->logParticipationByCustomer("Error enqueueing participation: $errorMessage");
                throw new Exception($errorMessage);
            }

        } else {
            $errorMessage = 'Sherpa code not available for the customer';
            $this->logger->logParticipationByCustomer("Error: $errorMessage");
            throw new Exception($errorMessage);
        }
    }

    /**
     * Maps participations to a specific format.
     *
     * @param array $participations The participations to map.
     * @return array The mapped participations.
     */
    public function normalizeParticipations(array $participations, $customerId): array
    {
        $mappedParticipations = [];

        foreach ($participations as $participation) {
            $mappedParticipation = $this->mapParticipation($participation, $customerId);
            $mappedParticipations[] = $mappedParticipation;
        }

        return $mappedParticipations;
    }

    public function getCustomerCodesRascasTable($customerId)
    {
        $rascasArray = [];
        try {
            $rascas = $this->rascasCollection->create();
            $rascas = $rascas->addFieldToFilter('customer_id', $customerId)->setOrder('date');
            $rascasArray = $rascas->getItems();
        } catch (Exception $e) {
        }

        return $rascasArray;
    }

    public function getWeekIdFromRasca($rascaId, $customerId)
    {
        $rascasCollectionTable = $this->getCustomerCodesRascasTable($customerId);
        foreach ($rascasCollectionTable as $rasca) {
            if ($rasca['rasca_code'] == $rascaId) {
                return $rasca['week_id'];
            }
        }

        return null;
    }

    public function getWeekTitle($weekId)
    {
        return $this->configAnniversary->getTitleWeekly($weekId);
    }

    /**
     * Maps a single participation to the desired format.
     *
     * @param array $participation The participation to map.
     * @return array The mapped participation.
     */
    private function mapParticipation(array $participation, $customerId): array
    {
        $couponUses = false;
        $imageDefault = $this->configAnniversary->getImageDefaultPrize();

        if (!is_null($participation["couponCode"])) {
            $couponInfo = $this->getCouponInfo($participation["couponCode"]);
            $imagePromo = $this->getImageByPromotion($couponInfo["promotionId"]);
            $couponUses = $couponInfo['uses']['uses'];
            $couponName = $couponInfo['couponName'];
        }

        $participationDate = $this->formatDate($participation["issuanceDate"] ?? "");
        $scratchDate = $this->formatDate($participation["scratchingDate"] ?? "");
        $raffleDate = $this->formatDate($participation["associationWithLotteryDate"] ?? "");
        $scratch = !(($participation["scratched"] === false));
        $weekTitle = $this->getWeekTitle($this->getWeekIdFromRasca($participation["raffleTicketCode"], $customerId));

        return [
            "raffle_id" => $participation["lotteryId"] ?? "",
            "participation_id" => $participation["raffleTicketCode"] ?? "",
            "participation_date" => $participationDate,
            "prize" => $couponName ?? "",
            "sale_id" => $participation["salesId"] ?? "",
            "coupon_code" => $participation["couponCode"] ?? "",
            "has_prize" => $participation["awarded"],
            "week_title" => $weekTitle ?? "",
            "prize_image_url" => $imagePromo ?? $imageDefault,
            "redeemed_prize" => $couponUses,
            "customer_id" => $participation["salesClient"] ?? "",
            "scratch" => $scratch,
            "store" => $participation["salesStoreCode"] ?? "",
            "scratch_date" => $scratchDate,
            "associated_raffle" => $participation["associatedWithLottery"],
            "raffle_date" => $raffleDate
        ];
    }

    public function formatDate($date): string
    {
        if (!empty($date)) {
            return date("d-m-Y", strtotime($date));
        } else {
            return "";
        }
    }

    public function filterParticipationsByScratch($participations, $scratchValue): array
    {
        $filteredParticipations = ["participations" => []];
        $raffleId = $this->configAnniversary->getIdRaffle();

        foreach ($participations as $participation) {
            if ($participation["scratch"] === $scratchValue && $participation["raffle_id"] == $raffleId) {
                $filteredParticipations["participations"][] = $participation;
            }
        }

        return $filteredParticipations;
    }

    public function orderParticipation($response)
    {
        usort($response["participations"], function ($a, $b) {
            $aKey = ($a["associatedWithLottery"] ? "0" : "1") . ($a["awarded"] ? "1" : "0");
            $bKey = ($b["associatedWithLottery"] ? "0" : "1") . ($b["awarded"] ? "1" : "0");

            return strcmp($bKey, $aKey);
        });

        return $response;
    }

    /**
     * @param  $couponCode
     * @return array|mixed
     */
    public function getCouponInfo($couponCode): mixed
    {
        return $this->coupon->getCoupon($couponCode);
    }

    public function getImageByPromotion($promotionCode)
    {
        $promotion = $this->redemptionRepository->getBySapPromotion($promotionCode);

        if ($promotion) {
            return $promotion->getImage();
        }

        return false;
    }
}
