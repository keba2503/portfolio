<?php

namespace Hiperdino\Anniversary2020\Model\Api;

use Exception;
use Hiperdino\Anniversary2020\Api\ScratchParticipationManagerInterface;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Participation\ManagerScratchParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Webapi\Rest\Request;
use Singular\EcommerceApp\Helper\Cart;
use Singular\EcommerceApp\Model\Api\ResponseManager as ResponseManager;

class ScratchParticipationManager implements ScratchParticipationManagerInterface
{
    protected ResponseManager $responseManager;
    protected CustomerRepositoryInterface $customerRepository;
    protected Request $request;
    protected Cart $ecommAppCartHelper;
    protected Logger $logger;
    protected CustomerManager $customerManager;
    protected ManagerScratchParticipation $scratchParticipation;

    public function __construct(
        ResponseManager $responseManager,
        CustomerRepositoryInterface $customerRepository,
        ManagerScratchParticipation $scratchParticipation,
        Request $request,
        CustomerManager $customerManager,
        Logger $logger
    ) {
        $this->responseManager = $responseManager;
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->customerManager = $customerManager;
        $this->scratchParticipation = $scratchParticipation;
    }

    /**
     * {@inheritdoc}
     */
    public function scratchRasca($customerId)
    {
        /** @var $customer Customer */
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logRegisterParticipation("Error: $message");

            return $this->responseManager->sendUnauth([], ResponseManager::TYPE_POST_RESPONSE);
        }

        $requestBody = json_decode($this->request->getContent(), true);

        $rascaCode = $requestBody['rasca_code'] ?? '';
        $message = 'Ha ocurrido algún error.';

        if ($rascaCode) {
            try {
                $this->scratchParticipation->scratchParticipation($customer, $rascaCode);
                $message = 'Se ha rascado la participación con exito';
                $response = [
                    'code' => ResponseManager::CODE_SUCCESS,
                    'message' => __($message)
                ];

                return $this->responseManager->sendOk($response, ResponseManager::TYPE_POST_RESPONSE);

            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->logger->logRegisterParticipation("Error: $message");
            }
        }

        $response = [
            'code' => ResponseManager::CODE_FAILURE,
            'message' => __($message)
        ];

        return $this->responseManager->sendBad($response, ResponseManager::TYPE_POST_RESPONSE);
    }
}
