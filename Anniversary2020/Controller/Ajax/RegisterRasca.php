<?php

namespace Hiperdino\Anniversary2020\Controller\Ajax;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Participation\ManagerRegisterParticipation;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class RegisterRasca extends Action
{
    protected Session $customerSession;
    protected JsonFactory $resultJsonFactory;
    protected RegisterParticipation $registerParticipation;
    protected Config $helperConfig;
    protected CustomerRepositoryInterface $customerRepository;
    protected ScopeConfigInterface $scopeConfig;
    protected CustomerManager $customerManagement;
    protected ManagerRegisterParticipation $participationManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory,
        RegisterParticipation $registerParticipation,
        Config $helperConfig,
        CustomerRepositoryInterface $customerRepository,
        ManagerRegisterParticipation $participationManager,
        CustomerManager $customerManagement
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->registerParticipation = $registerParticipation;
        $this->helperConfig = $helperConfig;
        $this->customerRepository = $customerRepository;
        $this->customerManagement = $customerManagement;
        $this->participationManager = $participationManager;

        return parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn() || !$this->helperConfig->isAnniversaryEnabled()) {
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('/')];

            return $resultJson->setData($response);
        }

        $rascaCode = trim($this->getRequest()->getParam('rascaCode', ''));
        if (!$rascaCode) {
            $response = ['success' => 0, 'message' => __('Debes introducir un código válido.')];

            return $resultJson->setData($response);
        }

        $customer = $this->customerSession->getCustomer();

        $canParticipate = $this->customerManagement->customerCanParticipate($customer);
        if (!$canParticipate) {
            $this->customerSession->setData(RegisterParticipation::SESSION_RASCA_NAME, $rascaCode);
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('hdanniversary/customer/edit')];

            return $resultJson->setData($response);
        }

        $isError = 0;
        $errorMessage = "";
        try {
            $customerData = $this->customerRepository->getById($customer->getId());
            $this->participationManager->registerParticipation($customerData, $rascaCode);
            $this->customerSession->unsetData(RegisterParticipation::SESSION_ERROR_NAME);
            $this->customerSession->setData(RegisterParticipation::SESSION_RASCA_NAME, $rascaCode);
            $this->customerSession->setData(RegisterParticipation::SESSION_REGISTERED_RASCA, true);
            $urlRedirect = $this->_url->getUrl('hdanniversary/success');
        } catch (Exception $e) {
            $this->customerSession->setData(RegisterParticipation::SESSION_ERROR_NAME, $e->getMessage());
            $this->customerSession->setData(RegisterParticipation::SESSION_RASCA_NAME, $rascaCode);
            $errorMessage = $e->getMessage();
            $urlRedirect = $this->_url->getUrl('hdanniversary/rasca/registerrasca');
            $isError = 1;
        }

        $response = ['success' => 1, 'redirectUrl' => $urlRedirect, 'isError' => $isError, 'message' => $errorMessage];
        $resultJson->setData($response);

        return $resultJson;
    }
}
