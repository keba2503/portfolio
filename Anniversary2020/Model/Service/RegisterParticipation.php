<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use DateTime;
use DateTimeZone;
use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Anniversary2020\Model\Customer\CustomerManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Singular\Islands\Model\IslandsRepository;

class RegisterParticipation
{
    const CODE = "anniversary2020";
    const SECTION = "hiperdino_anniversary2020";
    const ANNIVERSARY2020_BDTABLE = "hiperdino_anniversary2020_rascas";
    const SESSION_RASCA_NAME = "rasca_code";
    const SESSION_ERROR_NAME = "rasca_error_message";
    const SESSION_REGISTERED_RASCA = "rasca_registered";
    const COOKIE_RASCA_NAME = "Anniversary2020";

    const TYPE_REGISTER_PARTICIPATION = 'register_participation';
    const PROCESS = 'process';
    const ERROR = 'error';

    protected Config $helperConfig;
    protected ResourceConnection $resourceConnection;
    protected CustomerRepositoryInterface $customerRepository;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected IslandsRepository $islandsRepository;
    protected CustomerManager $customerManagement;
    protected Logger $log;
    protected RaffleWebService $raffleWebService;
    protected Queue $participationQueue;

    public function __construct(
        Config $helperConfig,
        ResourceConnection $resourceConnection,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        IslandsRepository $islandsRepository,
        CustomerManager $customerManagement,
        Logger $log,
        RaffleWebService $raffleWebService,
        Queue $participationQueue
    ) {
        $this->helperConfig = $helperConfig;
        $this->resourceConnection = $resourceConnection;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->islandsRepository = $islandsRepository;
        $this->customerManagement = $customerManagement;
        $this->log = $log;
        $this->raffleWebService = $raffleWebService;
        $this->participationQueue = $participationQueue;
    }

    /**
     * @throws Exception
     */
    public function registerRasca($customer, $rascaCode)
    {
        $this->log->logRegisterParticipation(__("Se comienza proceso de registrar en sorteo base de datos interna la participacion: " . $rascaCode));

        if (!$this->helperConfig->isPromotionAvailable()) {
            throw new Exception($this->helperConfig->getMessageOutPromotion());
        }

        if (!$this->customerManagement->customerCanRegisterOtherRasca($customer)) {
            throw new Exception($this->helperConfig->getErrorMaxWrongRascasRegistered());
        }
        $this->registerRascaBdd($rascaCode, $customer->getId());
    }

    protected function registerRascaBdd($rascaCode, $customerId)
    {
        $timezone = new DateTimeZone('Atlantic/Canary');
        $today = new DateTime('now', $timezone);
        $today = $today->format('Y-m-d H:i:s');

        $weekId = (string)$this->helperConfig->getActiveWeek();

        try {
            $connection = $this->resourceConnection->getConnection();
            $query = "INSERT INTO " . self::ANNIVERSARY2020_BDTABLE . " (customer_id, date, week_id, rasca_code) VALUES (?, ?, ?, ?)";
            $connection->query($query, [$customerId, $today, $weekId, $rascaCode]);
        } catch (Exception $e) {
            $this->log->logRegisterParticipation("Error al actualizar el rasca " . $rascaCode . ". Error " . $e->getMessage());
        }
    }

    public function resetAllNumWrongRascasRegister()
    {
        try {
            $connection = $this->resourceConnection->getConnection();

            $attributeId = $this->getAttributeId($connection, 'num_wrong_rascas_registered');

            if ($attributeId) {
                $this->log->Log("El id del atributo del customer 'num_wrong_rascas_registered' es: " . $attributeId);

                $this->deleteAttributes($connection, $attributeId);
            } else {
                $this->log->logRegisterParticipation("No existe el atributo 'num_wrong_rascas_registered'");
            }
        } catch (Exception $e) {
            $this->log->logRegisterParticipation("Error al resetear la variable 'num_wrong_rascas_registered': " . $e->getMessage());
        }
    }

    private function getAttributeId($connection, $attributeCode)
    {
        $query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = ?";
        $attributeId = $connection->fetchOne($query, [$attributeCode]);

        return $attributeId;
    }

    private function deleteAttributes($connection, $attributeId)
    {
        $query = "DELETE FROM customer_entity_varchar WHERE attribute_id = ?";
        $connection->query($query, [$attributeId]);
    }

    public function getAllIslands()
    {
        $islandArray = array();
        $searchCriteria = $this->searchCriteriaBuilder;
        $searchCriteria = $searchCriteria->create();
        $islands = $this->islandsRepository->getList($searchCriteria);

        foreach ($islands->getItems() as $island) {
            $id = $island->getId();
            $name = $island->getName();
            $islandArray[$id] = $name;
        }

        return $islandArray;
    }
}