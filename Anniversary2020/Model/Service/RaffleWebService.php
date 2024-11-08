<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Zend_Http_Client;
use Zend_Http_Client_Exception;

class RaffleWebService
{
    const STATUS_CODE_OK = 200;
    const PARTICIPATIONS_NOT_FOUND = 'No se han encontrado participaciones para el cliente';

    protected Config $raffleConfig;
    protected Zend_Http_Client $curlClient;
    protected Logger $logger;
    protected string $token = '';

    public function __construct(
        Config $raffleConfig,
        Zend_Http_Client $curlClient,
        Logger $logger,
    ) {
        $this->curlClient = $curlClient;
        $this->logger = $logger;
        $this->raffleConfig = $raffleConfig;
    }

    /**
     * @throws Exception
     */
    public function getParticipationByCustomer($customerId)
    {
        try {
            $baseUrl = (string)$this->raffleConfig->getBaseUrl();

            $endpoint = $baseUrl . "/lottery/customer/{customer}";
            $url = str_replace('{customer}', $customerId, $endpoint);

            try {
                $this->curlClient = $this->requestClientUrl($url, null, 'GET');

                $response = $this->curlClient->request()->getBody();
                $this->logger->logComerzziaEndpoint("Response participation by customer (" . $customerId . "):  $response");

            } catch (\Exception $e) {
                $this->logger->logComerzziaEndpoint("Error: " . $e->getMessage());

                return ['participations' => []];
            }

            $httpStatusCode = $this->curlClient->request()->getStatus();
            $responseEncode = json_decode($response, true);

            if (isset($responseEncode['message']) && $responseEncode['message'] === self::PARTICIPATIONS_NOT_FOUND) {
                $this->logger->logComerzziaEndpoint("Error: " . self::PARTICIPATIONS_NOT_FOUND);

                return ['participations' => []];
            }

            if ($httpStatusCode !== self::STATUS_CODE_OK) {
                $message = 'El servidor respondió con un código de estado no válido: ' . $httpStatusCode;
                $this->logger->logComerzziaEndpoint("Error: $message");

                throw new Exception($message);
            }

            return ['participations' => $responseEncode];

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logComerzziaEndpoint("Error: $message");
            throw new Exception($message);
        }
    }

    /**
     * @throws Exception
     */
    public function getParticipationById($raffleTicketCode)
    {
        try {
            $lotteryId = $this->raffleConfig->getIdRaffle();

            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $endpoint = $baseUrl . "/lottery/{raffleTicketCode}?lotteryId={lotteryId}";

            $url = str_replace('{raffleTicketCode}', urlencode($raffleTicketCode), $endpoint);
            $url = str_replace('{lotteryId}', $lotteryId, $url);
            $this->logger->logComerzziaEndpoint(__("**** Comienza Consulta de participacion por ID ****"));

            $this->logger->logComerzziaEndpoint("Request:  lotteryId = " . $lotteryId . " raffleTicketCode = " . $raffleTicketCode);

            try {
                $this->curlClient = $this->requestClientUrl($url, null, 'GET');

                $response = $this->curlClient->request()->getBody();
                $this->logger->logComerzziaEndpoint("Response participation query by ID: $response");

            } catch (\Exception $e) {
                $message = 'Error al realizar la solicitud.';
                $this->logger->logComerzziaEndpoint("Error: " . $e->getMessage());
                throw new Exception($message, 0, $e);
            }

            $httpStatusCode = $this->curlClient->request()->getStatus();

            if ($httpStatusCode !== self::STATUS_CODE_OK) {

                $responseEncode = json_decode($response, true);

                if (isset($responseEncode['type']) && $responseEncode['type'] === 'error') {
                    $message = $responseEncode['message'];
                    if (str_contains($message, "No se ha encontrado el sorteo con código")) {
                        $message = "La participación no existe en el sorteo actual.";
                    }

                    $this->logger->logComerzziaEndpoint("Error: $message");

                    throw new Exception($message, $httpStatusCode);
                }

                $message = 'Error de conexión' . $httpStatusCode;
                $this->logger->logComerzziaEndpoint("Error: $message");
                throw new Exception($message, $httpStatusCode);
            }

            return json_decode($response, true);

        } catch (Exception $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            throw new Exception($message, $code);
        }
    }

    /**
     * @param string $raffleTicketCode
     * @return array
     * @throws Exception
     */
    public function registerRaffle($raffleTicketCode)
    {
        try {
            $lotteryId = $this->raffleConfig->getIdRaffle();

            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $endpoint = $baseUrl . "/lottery/{raffleTicketCode}/assignToLottery?lotteryId={lotteryId}";

            $url = str_replace('{raffleTicketCode}', $raffleTicketCode, $endpoint);
            $url = str_replace('{lotteryId}', $lotteryId, $url);

            $this->logger->logComerzziaEndpoint(__("**** Comienza registro en el sorteo ****"));

            $requestClient = $this->requestClientUrl($url, [], 'PUT');

            $response = $requestClient->request();

            $responseData = $response->getBody();
            $this->logger->logComerzziaEndpoint("Response register raffle: $responseData");

            $responseArray = json_decode($responseData ?: "", true);

            if (!$response->isSuccessful()) {
                if (is_array($responseArray) && key_exists('message', $responseArray)) {
                    throw new Exception($responseArray['message']);
                }
                throw new Exception($response->getMessage());
            }

            if (!is_array($responseArray)) {
                throw new Exception('No se ha recibido una respuesta en formato JSON.');
            }

            return $responseArray;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logComerzziaEndpoint("Error: $message");
            throw new Exception($message);
        }
    }

    /**
     * @param string $raffleTicketCode
     * @return array
     * @throws Exception
     */
    public function scratchParticipation($raffleTicketCode)
    {
        try {
            $lotteryId = $this->raffleConfig->getIdRaffle();

            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $endpoint = $baseUrl . "/lottery/{raffleTicketCode}/scratch?lotteryId={lotteryId}";

            $url = str_replace('{raffleTicketCode}', $raffleTicketCode, $endpoint);
            $url = str_replace('{lotteryId}', $lotteryId, $url);

            try {
                $this->logger->logComerzziaEndpoint(__("**** Comienza rascado de participación ****"));

                $requestClient = $this->requestClientUrl($url, [], 'PUT');

                $response = $requestClient->request();

                $responseData = $response->getBody();
                $this->logger->logComerzziaEndpoint("Response: $responseData");

                $responseArray = json_decode($responseData ?: "", true);
            } catch (\Exception $e) {
                $message = 'Error al realizar la solicitud.';
                $this->logger->logComerzziaEndpoint("Error: " . $e->getMessage());
                throw new Exception($message, 0, $e);
            }

            if (!$response->isSuccessful()) {
                if (is_array($responseArray) && key_exists('message', $responseArray)) {
                    throw new Exception($responseArray['message']);
                }
                throw new Exception($response->getMessage());
            }

            if (!is_array($responseArray)) {
                throw new Exception('No se ha recibido una respuesta en formato JSON.');
            }

            return $responseArray;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logComerzziaEndpoint("Error: $message");
            throw new Exception($message);
        }
    }

    /**
     * @param string $participationId
     * @param string $customerId
     * @return array
     * @throws Exception
     */
    public function associatedCustomerParticipation($participationId, $customerId)
    {
        try {
            $lotteryId = $this->raffleConfig->getIdRaffle();

            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $endpoint = $baseUrl . "/lottery/{raffleTicketCode}/assignToCustomer?lotteryId={lotteryId}&customer={customer}";

            $url = str_replace('{raffleTicketCode}', $participationId, $endpoint);
            $url = str_replace('{customer}', $customerId, $url);
            $url = str_replace('{lotteryId}', $lotteryId, $url);

            $this->logger->logComerzziaEndpoint(__("**** Comienza asociación de participación al cliente ****"));
            $requestClient = $this->requestClientUrl($url, [], 'PUT');

            $response = $requestClient->request();

            $responseData = $response->getBody();
            $this->logger->logComerzziaEndpoint("Response associated customer participation: $responseData");

            $responseArray = json_decode($responseData ?: "", true);

            if (!$response->isSuccessful()) {
                if (is_array($responseArray) && key_exists('message', $responseArray)) {
                    throw new Exception($responseArray['message']);
                }
                throw new Exception($response->getMessage());
            }

            if (!is_array($responseArray)) {
                throw new Exception('No se ha recibido una respuesta en formato JSON.');
            }

            return $responseArray;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logComerzziaEndpoint("Error: $message");
            throw new Exception($message);
        }
    }

    /**
     * @throws Exception
     */
    public function requestParticipations($saleId, $numberOfParticipations, $purchaseDate, $store, $customerId, $amountSale)
    {
        try {
            $lotteryId = $this->raffleConfig->getIdRaffle();

            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $endpoint = $baseUrl . "/lottery";

            $purchaseDate = date('c', strtotime($purchaseDate));

            $requestBody = [
                'lotteryId' => $lotteryId,
                'salesTicketCode' => $saleId,
                'tillCode' => '',
                'numberOfRaffleTickets' => $numberOfParticipations,
                'salesDate' => $purchaseDate,
                'salesStoreCode' => $store,
                'salesClient' => $customerId,
                'amountSale' => $amountSale,
            ];

            $requestClient = $this->requestClientUrl($endpoint, $requestBody, 'POST');

            $this->logger->logComerzziaEndpoint("Request BODY:");
            $this->logger->logComerzziaEndpoint(json_encode($requestBody));

            $response = $requestClient->request();

            $responseData = $response->getBody();
            $this->logger->logComerzziaEndpoint("Response : $responseData");

            $responseArray = json_decode($responseData ?: "", true);
            $this->logger->logRequestParticipation("Response request participations: " . $responseData);

            if (!$response->isSuccessful()) {
                if (is_array($responseArray) && key_exists('message', $responseArray)) {
                    throw new Exception($responseArray['message']);
                }
                throw new Exception($response->getMessage());
            }

            if (!is_array($responseArray)) {
                throw new Exception('No response received in JSON format.');
            }

            return $responseArray;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->logComerzziaEndpoint("Error: $message");
            throw new Exception($message);
        }
    }

    /**
     * @param string $requestUrl
     * @param array $params
     * @param string $method
     * @return Zend_Http_Client
     * @throws Zend_Http_Client_Exception
     */
    protected function requestClientUrl($requestUrl, $params = [], $method = 'GET')
    {
        $token = $this->getToken();

        $client = new Zend_Http_Client();
        $client->setConfig(["timeout" => 3]);

        $client->setHeaders(['Content-Type' => 'application/json']);

        if ($token) {
            $client->setHeaders(['Authorization' => 'Bearer ' . $token]);
        }

        if ($params) {
            $requestBody = json_encode($params);
            $client->setRawData($requestBody);
        }

        $client->setMethod($method);
        $client->setUri($requestUrl);

        return $client;
    }

    /**
     * @return string|null
     * @throws Zend_Http_Client_Exception
     */
    protected function getToken()
    {
        if (!$this->token) {
            $loginUidactividad = (string)$this->raffleConfig->getUid();
            $loginUsername = (string)$this->raffleConfig->getUsername();
            $loginPassword = (string)$this->raffleConfig->getPassword();
            $loginData = [
                'uidActividad' => $loginUidactividad,
                'username' => $loginUsername,
                'password' => $loginPassword,
            ];
            $baseUrl = (string)$this->raffleConfig->getBaseUrl();
            $loginUrl = $baseUrl . "/login";
            $client = new Zend_Http_Client();
            $client->setConfig(["timeout" => 3]);
            $client->setMethod('POST');
            $client->setHeaders(['Content-Type' => "application/json"]);
            $client->setRawData(json_encode($loginData));
            $client->setUri($loginUrl);
            $response = $client->request();
            if ($response->isSuccessful()) {
                $responseData = json_decode($response->getBody() ?: "", true);
                if (isset($responseData['token'])) {
                    $this->token = $responseData['token'];
                }
            }
        }

        return $this->token;
    }
}