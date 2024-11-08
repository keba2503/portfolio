<?php

namespace Hiperdino\Anniversary2020\Controller\Ajax;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Hiperdino\Anniversary2020\Model\Participation\ManagerRegisterParticipation;
use Hiperdino\Anniversary2020\Model\ResourceModel\RaffleRgpd\CollectionFactory;
use Hiperdino\Anniversary2020\Model\Service\CustomerRgpdForm;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class CustomerEditPost extends Action
{
    protected Session $customerSession;
    protected Config $helperConfig;
    protected JsonFactory $resultJsonFactory;
    protected RegisterParticipation $registerParticipation;
    protected CustomerRepositoryInterface $customerRepository;
    protected CollectionFactory $raffleRgpdFactory;
    protected CustomerRgpdForm $customerRgpdForm;
    protected ManagerRegisterParticipation $participationManager;
    protected CustomerManager $customerManager;

    public function __construct(
        Config $helperConfig,
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        RegisterParticipation $registerParticipation,
        CustomerRepositoryInterface $customerRepository,
        CollectionFactory $raffleRgpdFactory,
        ManagerRegisterParticipation $participationManager,
        CustomerRgpdForm $customerRgpdForm,
        CustomerManager $customerManager
    ) {
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->registerParticipation = $registerParticipation;
        $this->customerRepository = $customerRepository;
        $this->helperConfig = $helperConfig;
        $this->raffleRgpdFactory = $raffleRgpdFactory;
        $this->customerRgpdForm = $customerRgpdForm;
        $this->participationManager = $participationManager;
        $this->customerManager = $customerManager;

        return parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn() || !$this->helperConfig->isPromotionAvailable()) {
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('/')];

            return $resultJson->setData($response);
        }

        $rascaCode = trim($this->customerSession->getData(RegisterParticipation::SESSION_RASCA_NAME) ?: "");
        if (!$rascaCode) {
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl($this->helperConfig->getUrlCmsAnniversary())];

            return $resultJson->setData($response);
        }

        $dni = $this->getRequest()->getParam('customer_dni');
        $customer = $this->customerSession->getCustomer();
        $customer = $this->customerRepository->getById($customer->getId());

        $customerTelephone = $this->getRequest()->getParam('customer_telephone');
        $customerIsland = $this->getRequest()->getParam('customer_island');
        $acceptAnniversary = $this->getRequest()->getParam('terms_and_conditions');

        if (!$dni || !$customerTelephone || !$customerIsland || $acceptAnniversary !== "retrieve") {
            $this->messageManager->addWarningMessage(__('Debe rellenar todos los campos y aceptar las condiciones de la promoción.'));
            $response = ['success' => 1, 'redirectUrl' => $this->_url->getUrl('hdanniversary/customer/edit')];

            return $resultJson->setData($response);
        }

        $customerCanParticipate = $this->customerManager->customerCanParticipate($this->customerSession->getCustomer());

        try {
            if (!$customerCanParticipate) {
                $customerData = $this->customerRgpdForm->customerRgpdForm($dni, $customerTelephone, $customerIsland, $customer->getId());
            } else {
                throw new Exception(__('Usuario registrado en el año en curso'));
            }
        } catch (Exception $e) {
            $response = ['success' => 0, 'message' => __($e->getMessage())];

            return $resultJson->setData($response);
        }

        $isError = 0;
        try {
            $this->participationManager->registerParticipation($customerData, $rascaCode);
            $this->customerSession->unsetData(RegisterParticipation::SESSION_RASCA_NAME);
            $redirectUrl = $this->_url->getUrl('hdanniversary/success');
        } catch (Exception $e) {
            $this->customerSession->setData(RegisterParticipation::SESSION_ERROR_NAME, $e->getMessage());
            $isError = 1;
            $redirectUrl = $this->_url->getUrl('hdanniversary/rasca/registerrasca');
        }

        $response = ['success' => 1, 'redirectUrl' => $redirectUrl, 'isError' => $isError];
        $resultJson->setData($response);

        return $resultJson;
    }
}
