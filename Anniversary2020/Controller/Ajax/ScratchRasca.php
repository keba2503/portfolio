<?php

namespace Hiperdino\Anniversary2020\Controller\Ajax;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Hiperdino\Anniversary2020\Model\Participation\ManagerScratchParticipation;

class ScratchRasca extends Action
{
    protected Session $customerSession;
    protected JsonFactory $resultJsonFactory;
    protected RegisterParticipation $registerParticipation;
    protected Config $helperConfig;
    protected CustomerRepositoryInterface $customerRepository;
    protected ScopeConfigInterface $scopeConfig;
    protected CustomerManager $customerManagement;
    protected ManagerScratchParticipation $scratchParticipation;

    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory,
        RegisterParticipation $registerParticipation,
        Config $helperConfig,
        CustomerRepositoryInterface $customerRepository,
        ManagerScratchParticipation $scratchParticipation,
        CustomerManager $customerManagement
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->registerParticipation = $registerParticipation;
        $this->helperConfig = $helperConfig;
        $this->customerRepository = $customerRepository;
        $this->customerManagement = $customerManagement;

        $this->scratchParticipation = $scratchParticipation;

        return parent::__construct($context);
    }

    public function execute()
    {
        $rascaCode = trim($this->getRequest()->getParam('rascaCode', ''));
        $customer = $this->customerSession->getCustomer();

        $resultJson = $this->resultJsonFactory->create();
        if (!$this->customerSession->isLoggedIn() || !$this->helperConfig->isAnniversaryEnabled()) {
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('/')];

            return $resultJson->setData($response);
        }

        if (!$rascaCode) {
            $response = ['success' => 0, 'message' => __('Debes introducir un código válido.')];

            return $resultJson->setData($response);
        }

        $isError = 0;
        $errorMessage = "";
        try {
            $this->scratchParticipation->scratchParticipation($customer, $rascaCode);
        } catch (Exception $e) {
            $this->customerSession->setData(RegisterParticipation::SESSION_ERROR_NAME, $e->getMessage());
            $isError = 1;
            $errorMessage = $e->getMessage();
        }

        $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('hdanniversary/success'), 'isError' => $isError, 'message' => $errorMessage];
        $resultJson->setData($response);

        return $resultJson;
    }
}
