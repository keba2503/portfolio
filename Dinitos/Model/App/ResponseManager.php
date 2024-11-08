<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\CustomerDinitosResponseInterface;
use Hiperdino\Dinitos\Api\Data\HistoryListInterface;
use Hiperdino\Dinitos\Api\Data\PackageListResponseInterface;
use Hiperdino\Dinitos\Api\Data\RewardListInterface;
use Hiperdino\Dinitos\Api\Data\RewardResponseListInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Webapi\Rest\Response;
use Singular\EcommerceApp\Api\Data\PostResponseInterface;
use Singular\EcommerceApp\Model\Data\PostResponse;

class ResponseManager
{
    const CODE_SUCCESS = '0';
    const CODE_FAILURE = '-1';

    const HTTP_OK = 200;
    const HTTP_BAD = 400;
    const HTTP_UNAUTH = 401;
    const HTTP_NOTFOUND = 404;

    const TYPE_HISTORY_LIST = 'history_list';
    const TYPE_POST_RESPONSE = 'post_response';
    const TYPE_DINITOS = 'dinitos';
    const TYPE_REWARDS_LIST = 'rewards_list';
    const TYPE_REWARDS_LIST_RESPONSE = 'rewards_list_response';
    const TYPE_PACKAGE_RESPONSE = 'package_response';

    public function __construct(
        protected Response $response,
        protected DataObjectHelper $dataObjectHelper,
        protected PostResponse $postResponse,
        protected HistoryList $historyList,
        protected CustomerDinitosResponseInterface $customerDinitosResponse,
        protected RewardList $rewardList,
        protected RewardResponseListInterface $rewardResponseList,
        protected PackageListResponseInterface $packageResponse
    ) {
    }

    public function send($httpCode, $dataArray, $type = null)
    {
        $this->response->setHttpResponseCode($httpCode);
        $resultObject = $this->getResultObject($type);
        $this->dataObjectHelper->populateWithArray(
            $resultObject,
            $dataArray,
            $this->getResultClass($type)
        );

        return $resultObject;
    }

    public function sendOk($dataArray, $type = null)
    {
        return $this->send(self::HTTP_OK, $dataArray, $type);
    }

    public function sendBad($dataArray, $type = null)
    {
        return $this->send(self::HTTP_BAD, $dataArray, $type);
    }

    public function sendUnauth($dataArray, $type = null)
    {
        return $this->send(self::HTTP_UNAUTH, $dataArray, $type);
    }

    public function sendNotfound($dataArray, $type = null)
    {
        return $this->send(self::HTTP_NOTFOUND, $dataArray, $type);
    }

    protected function getResultObject($type)
    {
        switch ($type) {
            case self::TYPE_HISTORY_LIST:
                return $this->historyList;
            case self::TYPE_POST_RESPONSE:
                return $this->postResponse;
            case self::TYPE_DINITOS:
                return $this->customerDinitosResponse;
            case self::TYPE_REWARDS_LIST:
                return $this->rewardList;
            case self::TYPE_REWARDS_LIST_RESPONSE:
                return $this->rewardResponseList;
            case self::TYPE_PACKAGE_RESPONSE:
                return $this->packageResponse;
        }
    }

    protected function getResultClass($type)
    {
        switch ($type) {
            case self::TYPE_HISTORY_LIST:
                return HistoryListInterface::class;
            case self::TYPE_POST_RESPONSE:
                return PostResponseInterface::class;
            case self::TYPE_DINITOS:
                return CustomerDinitosResponseInterface::class;
            case self::TYPE_REWARDS_LIST:
                return RewardListInterface::class;
            case self::TYPE_REWARDS_LIST_RESPONSE:
                return RewardResponseListInterface::class;
            case self::TYPE_PACKAGE_RESPONSE:
                return PackageListResponseInterface::class;
        }
    }
}
