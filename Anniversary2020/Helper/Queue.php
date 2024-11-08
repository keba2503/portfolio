<?php

namespace Hiperdino\Anniversary2020\Helper;

use Exception;
use Hiperdino\Anniversary2020\Model\Service\RaffleWebService;
use Hiperdino\Crm\Helper\Customer;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Singular\FlagManager\Model\Services\FlagManager;

class Queue
{
    const PARTICIPATION_FLAG = 'participation.flag';
    const QUEUE_TABLE = "hiperdino_participation_queue";

    const PROCESS = 'process';
    const PENDING = 'pending';
    const ERROR = 'error';
    const REQUEST_PARTICIPATION = 'request_participation';

    protected ResourceConnection $resourceConnection;
    protected DataObjectHelper $dataObjectHelper;
    protected TimezoneInterface $timezoneInterface;
    protected Config $helperConfig;
    protected Logger $logger;
    protected RaffleWebService $raffleWebService;
    protected ManagerInterface $eventManager;
    protected FlagManager $flagManager;
    protected Customer $crmHelper;

    public function __construct(
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        Config $helperConfig,
        TimezoneInterface $timezoneInterface,
        RaffleWebService $raffleWebService,
        ManagerInterface $eventManager,
        FlagManager $flagManager,
        Logger $logger,
        Customer $crmHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->timezoneInterface = $timezoneInterface;
        $this->helperConfig = $helperConfig;
        $this->raffleWebService = $raffleWebService;
        $this->eventManager = $eventManager;
        $this->flagManager = $flagManager;
        $this->logger = $logger;
        $this->crmHelper = $crmHelper;
    }

    /**
     * @throws Exception
     */
    public function processParticipationQueue()
    {
        if (!$this->flagManager->generateFlag(self::PARTICIPATION_FLAG)) {
            $this->log("**** El proceso anterior no ha terminado. ****");

            return;
        }
        $this->log("*** Comienza cola procesado ***");

        $maxTries = $this->helperConfig->getMaxQueueTries();
        $notProcessedParticipationQueue = $this->getNotProcessedParticipation();

        foreach ($notProcessedParticipationQueue as $participationQueue) {
            $participationQueueId = $participationQueue['id'];
            $tries = $participationQueue['tries'] + 1;

            try {
                $request = $participationQueue['body'];
                $participationData = json_decode($request, true);
                $numberPrefixToRemove = 'c';

                $requiredFields = ['salesTicketCode', 'numberOfRaffleTickets', 'salesDate', 'salesStoreCode', 'salesClient', 'amountSale'];
                foreach ($requiredFields as $field) {
                    if (!isset($participationData[$field])) {
                        throw new Exception("Required field '{$field}' is missing in the request data.");
                    }
                }

                $salesTicketCode = $participationData['salesTicketCode'];
                $numberOfRaffleTickets = $participationData['numberOfRaffleTickets'];
                $salesDate = $participationData['salesDate'];
                $salesStoreCode = $this->removePrefix($participationData['salesStoreCode'], $numberPrefixToRemove);
                $salesClient = $participationData['salesClient'];
                $amountSale = $participationData['amountSale'];

                $this->logger->logRequestParticipation(__('Se comienza proceso de solicitud de participaciones'));
                $response = $this->raffleWebService->requestParticipations($salesTicketCode, $numberOfRaffleTickets, $salesDate, $salesStoreCode, $salesClient, $amountSale);

                $customer = $this->crmHelper->getCustomerBySherpaCode($salesClient);
                if ($customer) {
                    $this->eventManager->dispatch("customer_obtained_participation", ["customer_id" => $customer->getId()]);
                }

                $this->markAsProcessed($participationQueueId, self::REQUEST_PARTICIPATION, $response);

            } catch (Exception $e) {
                $message = $e->getMessage();
                if ($tries >= $maxTries) {
                    $this->markAsError($participationQueueId, $message, self::REQUEST_PARTICIPATION );
                } else {
                    $this->updateTries($tries, $message, $participationQueueId);
                }
            }
        }

        $this->log("*** Fin cola procesado ***");
        $this->flagManager->removeFlag(self::PARTICIPATION_FLAG);
    }

    /**
     *
     * @param string $request
     * @param string $response
     * @param $customerId
     * @return int
     */
    public function queueParticipation(string $request, string $response, $customerId): int
    {
        $currentTime = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
        $data = [
            'body' => $request,
            'response' => $response,
            'created_at' => $currentTime,
            'tries' => 0,
            'status' => 0,
            'message' => '',
            'updated_at' => $currentTime,
            'id_customer' => $customerId
        ];

        $this->resourceConnection->getConnection()->insert(self::QUEUE_TABLE, $data);

        return (int)$this->resourceConnection->getConnection()->lastInsertId();
    }

    public function getParticipation($queueId)
    {
        $query = "SELECT * FROM " . self::QUEUE_TABLE . " WHERE id = {$queueId}";

        return $this->resourceConnection->getConnection()->fetchRow($query);
    }

    public function getNotProcessedParticipation(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(self::QUEUE_TABLE);

        $query = $connection->select()
            ->from($table)
            ->where('status = ?', 0)
            ->where('type = ?', self::REQUEST_PARTICIPATION);

        return $connection->fetchAll($query);
    }

    public function getErrorsParticipationQueue(): array
    {
        $query = "SELECT * FROM " . self::QUEUE_TABLE . " WHERE status = 2";

        return $this->resourceConnection->getConnection()->fetchAll($query);
    }

    /**
     * @param $queueId
     * @param $reference
     * @return void
     */
    public function markAsPending($queueId, $reference): void
    {
            $this->resourceConnection->getConnection()->query("UPDATE " . self::QUEUE_TABLE . " SET status = 0, type = '{$reference}' WHERE id = {$queueId}");
    }

    /**
     *
     * @param int $queueId
     * @param string $type
     * @return void
     */
    public function markAsProcessed($queueId, $type, $response)
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(self::QUEUE_TABLE);

        if (is_array($response) || is_object($response)) {
            $responseString = $this->getResponseJSON($response);
        } else {
            $responseString = (string)$response;
        }

        $data = [
            'status' => 1,
            'type' => $type,
            'response' => $responseString,
        ];

        $where = ['id = ?' => (int)$queueId];

        $connection->update($table, $data, $where);
    }

    /**
     *
     * @param mixed $response
     * @return string
     */
    private function getResponseJSON($response)
    {
        if (is_array($response) && count($response) > 0) {
            return json_encode($response[0]);
        }

        return '[]';
    }

    /**
     * Marcar como error en la cola.
     *
     * @param int $queueId
     * @param string $errorMessage
     * @param string $type
     * @return void
     */
    public function markAsError(int $queueId, string $errorMessage, string $type): void
    {
        $this->resourceConnection->getConnection()->query("
        UPDATE " . self::QUEUE_TABLE . " SET status = 2, message = '{$errorMessage}', type = '{$type}' WHERE id = {$queueId}
    ");
    }

    /**
     * @param $tries
     * @param string $message
     * @param $queueId
     * @return void
     */
    protected function updateTries($tries, string $message, $queueId): void
    {
        $this->resourceConnection->getConnection()->query("
                UPDATE " . self::QUEUE_TABLE . " SET tries = {$tries}, message = '{$message}' WHERE id = {$queueId}
        ");
    }

    public function cleanOldRegisters()
    {
        try {
            $removeDaysSuccess = $this->helperConfig->getMaxDaysQueueRegisterSuccess();
            $removeDateSuccess = date("Y-m-d 00:00:00", strtotime("-" . $removeDaysSuccess . " day"));
            $removeDaysErrors = $this->helperConfig->getMaxDaysQueueRegisterErrors();
            $removeDateErrors = date("Y-m-d 00:00:00", strtotime("-" . $removeDaysErrors . " day"));

            $connection = $this->resourceConnection->getConnection();
            $query = "DELETE FROM " . self::QUEUE_TABLE . " WHERE (updated_at <= '{$removeDateSuccess}' AND status =1) OR (updated_at <= '{$removeDateErrors}' AND status = 2)";
            $connection->query($query);
            $connection->query($query);
        } catch (Exception $e) {
            $this->logger->log($e->getMessage());
        }
    }

    /**
     * @param bool|string $response
     * @param string $type
     * @param string $message
     * @param string $request
     * @param $reference
     * @return true
     */
    public function enqueue(bool|string $response, string $type, string $request, $reference, $customerId, string $message = '')
    {
        if ($type === self::PENDING) {
            $queueId = $this->queueParticipation($request, $response, $customerId);
            $this->markAsPending($queueId, $reference);
        }

        if ($type === self::PROCESS && $this->helperConfig->getHistoryQueueEnabled()) {
            $queueId = $this->queueParticipation($request, $response, $customerId);
            $this->markAsProcessed($queueId, $reference, $response);
        }

        if ($type === self::ERROR && $this->helperConfig->getHistoryQueueEnabled()) {
            $queueId = $this->queueParticipation($request, $response, $customerId);
            $this->markAsError($queueId, $message, $reference);
        }

        return true;
    }

    private function removePrefix($value, $prefix)
    {
        if (str_starts_with($value, $prefix)) {
            return substr($value, strlen($prefix));
        }

        return $value;
    }

    private function log($message)
    {
        $this->logger->logRequestParticipation($message);
    }
}

