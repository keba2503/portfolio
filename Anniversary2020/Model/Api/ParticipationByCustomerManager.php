<?php

namespace Hiperdino\Anniversary2020\Model\Api;

use Exception;
use Hiperdino\Anniversary2020\Api\ParticipationByCustomerManagerInterface;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\App\ResponseManager;
use Hiperdino\Anniversary2020\Model\Participation\ManagerParticipationByCustomer;
use Hiperdino\Anniversary2020\Model\Participation\ManagerScratchParticipation;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Magento\Framework\App\RequestInterface;

class ParticipationByCustomerManager implements ParticipationByCustomerManagerInterface
{
    protected RaffleWebService $raffleWebService;
    protected Logger $logger;
    protected ResponseManager $responseManager;
    protected ManagerParticipationByCustomer $partcipationByCutomer;
    protected RequestInterface $request;
    protected ManagerScratchParticipation $scratchParticipation;

    public function __construct(
        RaffleWebService $raffleWebService,
        Logger $logger,
        RequestInterface $request,
        ResponseManager $responseManager,
        ManagerParticipationByCustomer $participationByCutomer,
        ManagerScratchParticipation $scratchParticipation,
    ) {
        $this->raffleWebService = $raffleWebService;
        $this->logger = $logger;
        $this->responseManager = $responseManager;
        $this->partcipationByCutomer = $participationByCutomer;
        $this->request = $request;
        $this->scratchParticipation = $scratchParticipation;
    }

    public function getParticipationByCustomer($customerId)
    {
        try {
            $scratch = !(($this->request->getParam('scratch') === 'false'));

            $response = $this->partcipationByCutomer->callParticipationByCustomer($customerId, $scratch);

            return $this->responseManager->sendOk($response, ResponseManager::PARTICIPATIONS_LIST);

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->logParticipationByCustomer("Error: $errorMessage");
            $response = ["participations" => []];

            return $this->responseManager->sendBad($response, ResponseManager::PARTICIPATIONS_LIST);
        }
    }
}
