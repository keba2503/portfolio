<?php

namespace Hiperdino\Dinitos\Model\App;

use Exception;
use Hiperdino\Dinitos\Api\AppManagerInterface;
use Hiperdino\Dinitos\Api\Data\CustomerDinitosResponseInterface;
use Hiperdino\Dinitos\Api\Data\HistoryListInterface;
use Hiperdino\Dinitos\Api\Data\PackageListResponseInterface;
use Hiperdino\Dinitos\Api\Data\RewardListInterface;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetDinitos;
use Hiperdino\Dinitos\Model\Services\CustomerDinitos\GetTotalDinitos;
use Hiperdino\Dinitos\Model\Services\History\GetMovementsCustomer;
use Hiperdino\Dinitos\Model\Services\Package\GetToExpire;
use Hiperdino\Dinitos\Model\Services\Rewards\GetAvailable;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Webapi\Rest\Request;

class AppManager implements AppManagerInterface
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected GetDinitos $customerDinitosService,
        protected GetAvailable $availableRewardsService,
        protected GetMovementsCustomer $historyByCustomerService,
        protected ResponseManager $responseManager,
        protected Logger $logger,
        protected Request $request,
        protected GetToExpire $customerPackageToExpire,
        protected Config $configHelper,
        protected GetTotalDinitos $customerAndQuoteDinitos
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getCustomerDinitos($customerId): CustomerDinitosResponseInterface
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledApp()) {
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("La configuración de dinitos no está habilitada en APP.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_DINITOS);
        }
        try {
            $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__($e->getMessage()));
            $this->responseManager->sendUnauth([], ResponseManager::TYPE_DINITOS);
        }
        try {
            $result = [
                'dinitos' => $this->customerDinitosService->getCustomerDinitosTotal($customerId),
                'code' => ResponseManager::CODE_SUCCESS,
                'message' => ''
            ];
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__("Error al obtener los dinitos del customer: \n {$e->getMessage()}"));
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("Ha ocurrido un error al obtener los dinitos del cliente")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_DINITOS);
        }

        return $this->responseManager->sendOk($result, ResponseManager::TYPE_DINITOS);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerDinitosHistory($customerId): HistoryListInterface
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledApp()) {
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("La configuración de dinitos no está habilitada en APP.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_HISTORY_LIST);
        }
        try {
            $customerHistory = $this->historyByCustomerService->callHistoryByCustomer($customerId) ?: [];
            $response = [
                'items' => $customerHistory,
                'code' => ResponseManager::CODE_SUCCESS,
                'message' => empty($customerHistory) ? "El cliente no tiene histórico" : ""
            ];
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__("Error al obtener el histórico de dinitos: \n {$e->getMessage()}"));
            $response = [
                'items' => [],
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("Ha ocurrido un error al obtener el histórico del cliente")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_HISTORY_LIST);
        }

        return $this->responseManager->sendOk($response, ResponseManager::TYPE_HISTORY_LIST);
    }

    /**
     * @inheritDoc
     */
    public function getStoreViewRewards($customerId = null): RewardListInterface
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledApp()) {
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("La configuración de dinitos no está habilitada en APP.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_REWARDS_LIST);
        }
        try {
            $requestUri = explode('/', $this->request->getRequestUri());
            $step = $this->request->getParam('step');
            $storeViewRewards = !is_null($step) ?
                $this->availableRewardsService->getStoreViewRewards($step) :
                $this->availableRewardsService->getStoreViewRewards();
            $stores = [];
            foreach ($storeViewRewards as $storeViewReward) {
                $stores[] = $storeViewReward->toArray();
            }
            $response = [
                'items' => $stores,
                'code' => ResponseManager::CODE_SUCCESS,
                'message' => empty($stores) ? "No hay recompensas para la tienda seleccionada" : ""
            ];
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__("Error al obtener las recompensas por website: \n {$e->getMessage()}"));
            $response = [
                'items' => [],
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("Ha ocurrido un error al obtener las recompensas del storeview")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_REWARDS_LIST);
        }

        return $this->responseManager->sendOk($response, ResponseManager::TYPE_REWARDS_LIST);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerClosestExpiredPackage($customerId): PackageListResponseInterface
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledApp()) {
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("La configuración de dinitos no está habilitada en APP.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_PACKAGE_RESPONSE);
        }
        try {
            $package = $this->customerPackageToExpire->getNextExpirablePackage($customerId);
            $response = [
                'packages' => $package,
                'code' => ResponseManager::CODE_SUCCESS,
                'message' => empty($package->toArray()) ? "El cliente no tiene paquetes próximos a caducar" : ""
            ];
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__("Error al obtener el paquete próximo a expirar del customer: \n {$e->getMessage()}"));
            $response = [
                'packages' => [],
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("Error al obtener el paquete próximo a expirar del customer")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_PACKAGE_RESPONSE);
        }

        return $this->responseManager->sendOk($response, ResponseManager::TYPE_PACKAGE_RESPONSE);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerDinitosPlusQuote($customerId)
    {
        if (!$this->configHelper->isDinitosAccumulatedEnabledApp()) {
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("La configuración de dinitos no está habilitada en APP.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_DINITOS);
        }
        try {
            $this->customerRepository->getById($customerId);
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__($e->getMessage()));
            $this->responseManager->sendUnauth([], ResponseManager::TYPE_DINITOS);
        }
        try {
            $totalDinitos = $this->customerAndQuoteDinitos->getTotalDinitosSum($customerId);
            $result = [
                'dinitos' => $totalDinitos,
                'code' => ResponseManager::CODE_SUCCESS,
                'message' => ''
            ];
        } catch (Exception $e) {
            $this->logger->logDinitosApp(__("Error al obtener los dinitos del carrito y el cliente: \n {$e->getMessage()}"));
            $response = [
                'code' => ResponseManager::CODE_FAILURE,
                'message' => __("Ha ocurrido un error al obtener los dinitos del carrito y el cliente.")
            ];

            return $this->responseManager->sendBad($response, ResponseManager::TYPE_DINITOS);
        }

        return $this->responseManager->sendOk($result, ResponseManager::TYPE_DINITOS);
    }
}