<?php

namespace Hiperdino\Anniversary2020\Model\Api;

use Exception;
use Hiperdino\Anniversary2020\Api\RegisterParticipationManagerInterface;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Participation\ManagerRegisterParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Webapi\Rest\Request;
use Singular\EcommerceApp\Helper\Cart;
use Singular\EcommerceApp\Model\Api\ResponseManager as ResponseManager;

class RegisterParticipationManager implements RegisterParticipationManagerInterface
{
    protected ResponseManager $responseManager;
    protected CustomerRepositoryInterface $customerRepository;
    protected Request $request;
    protected Cart $ecommAppCartHelper;
    protected Logger $logger;
    protected CustomerManager $customerManager;
    protected ManagerRegisterParticipation $registerParticipation;

    public function __construct(
        ResponseManager $responseManager,
        CustomerRepositoryInterface $customerRepository,
        Request $request,
        ManagerRegisterParticipation $registerParticipation,
        CustomerManager $customerManager,
        Logger $logger
    ) {
        $this->responseManager = $responseManager;
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->customerManager = $customerManager;
        $this->registerParticipation = $registerParticipation;
    }

    /**
     * {@inheritdoc}
     */
    public function registerRasca($customerId)
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

        if (!preg_match('/^[A-Za-z0-9Ññ]+$/', $rascaCode)) {
            throw new Exception(__('Formato no válido.'));
        }

        $message = 'Ha ocurrido algún error.';

        $canParticipate = $this->customerManager->customerCanParticipate($customer);

        if (!$canParticipate) {
            $message = 'El cliente no puede participar por no estar asociado al sorteo RGPD del año en curso.';
            $response = [
                'code' => ResponseManager::CODE_FAILURE_RGPD,
                'message' => __($message)
            ];

            return $this->responseManager->sendOk($response, ResponseManager::TYPE_POST_RESPONSE);
        }

        if ($rascaCode) {
            try {
                $this->registerParticipation->registerParticipation($customer, $rascaCode);
                $message = 'Código registrado con éxito';
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
