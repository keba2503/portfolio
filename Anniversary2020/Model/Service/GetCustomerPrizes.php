<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Exception;
use Hiperdino\Homai\Helper\Services;
use Magento\Customer\Api\CustomerRepositoryInterface;

class GetCustomerPrizes
{
    protected RaffleWebService $raffleWebService;
    protected Services $homaiServices;
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(
        RaffleWebService $raffleWebService,
        CustomerRepositoryInterface $customerRepository,
        Services $homaiServices
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->homaiServices = $homaiServices;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @throws Exception
     */
    public function execute($customerId)
    {
        $customerParticipations = [];
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            return $customerParticipations;
        }

        $sherpaCode = $customer->getCustomAttribute('sherpa_code');
        if ($sherpaCode && $sherpaCode = $sherpaCode->getValue()) {
            $participations = $this->raffleWebService->getParticipationByCustomer($sherpaCode);
            if (key_exists("participations", $participations) && $participations['participations']) {
                $participations = $participations['participations'];
                foreach ($participations as $participation) {
                    if ($participation['awarded'] && $participation['scratched']) {
                        $couponData = $this->homaiServices->getCoupon($participation['couponCode']);
                        $participation['couponData'] = $couponData;
                        $customerParticipations[] = $participation;
                    }
                }
            }
        }

        return $customerParticipations;
    }
}