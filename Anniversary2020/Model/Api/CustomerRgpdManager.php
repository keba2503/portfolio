<?php

namespace Hiperdino\Anniversary2020\Model\Api;

use Exception;
use Hiperdino\Anniversary2020\Api\CustomerRgpdManagerInterface;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\CustomerRgpdForm;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Webapi\Rest\Request;
use Singular\EcommerceApp\Model\Api\ResponseManager;

class CustomerRgpdManager implements CustomerRgpdManagerInterface
{
    protected ResponseManager $responseManager;
    protected CustomerRepositoryInterface $customerRepository;
    protected Request $request;
    protected Logger $logger;
    protected CustomerRgpdForm $customerRgpdForm;
    protected CustomerManager $customerManagement;
    protected Config $config;

    public function __construct(
        ResponseManager $responseManager,
        CustomerManager $customerManagement,
        CustomerRepositoryInterface $customerRepository,
        Request $request,
        CustomerRgpdForm $customerRgpdForm,
        Logger $logger,
        Config $config
    ) {
        $this->responseManager = $responseManager;
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->customerRgpdForm = $customerRgpdForm;
        $this->customerManagement = $customerManagement;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function customerRgpd($customerId)
    {
        /** @var $customer Customer */
        try {
            $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logRegisterCustomeRgpd("Error: $message");

            return $this->responseManager->sendUnauth([], ResponseManager::TYPE_POST_RESPONSE);
        }

        $customerData = $this->customerRepository->getById($customerId);
        $customerCanParticipate = $this->customerManagement->customerCanParticipate($customerData);

        if ($customerCanParticipate) {
            $message = 'El cliente ya esta registrado en el RGPD del año en curso';
            $responseCode = ResponseManager::CODE_FAILURE;
        } else {
            $requestBody = json_decode($this->request->getContent(), true);

            $taxvat = $requestBody['taxvat'] ?? '';
            $phone = $requestBody['phone'] ?? '';
            $island = $requestBody['island'] ?? '';

            $message = 'Ha ocurrido algún error.';

            if ($taxvat && $phone && $island) {
                try {
                    $this->customerRgpdForm->customerRgpdForm($taxvat, $phone, $island, $customerId);

                    $message = $this->config->getTextRgpd();
                    $responseCode = ResponseManager::CODE_SUCCESS;
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $this->logger->logRegisterCustomeRgpd("Error: $message");
                    $responseCode = ResponseManager::CODE_FAILURE;
                }
            } else {
                $responseCode = ResponseManager::CODE_FAILURE;
            }
        }

        $response = [
            'code' => $responseCode,
            'message' => __($message)
        ];

        return $this->responseManager->sendOk($response, ResponseManager::TYPE_POST_RESPONSE);
    }
}
