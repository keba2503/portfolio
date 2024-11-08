<?php

namespace Hiperdino\Anniversary2020\Model\App;

use Hiperdino\Anniversary2020\Api\Data\ParticipationListInterface;
use Hiperdino\Anniversary2020\Model\Data\ParticipationList;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Webapi\Rest\Response;
use Singular\EcommerceApp\Model\Data\PostResponse;

class ResponseManager
{
    const CODE_SUCCESS = '0';
    const CODE_FAILURE = '-1';

    const HTTP_OK = 200;
    const HTTP_BAD = 400;
    const HTTP_UNAUTH = 401;
    const HTTP_NOTFOUND = 404;

    const PARTICIPATIONS_LIST = 'participations_list';
    const PARTICIPATIONS_FAILURE = 'participation_failure';

    protected Response $response;
    protected DataObjectHelper $dataObjectHelper;
    protected ParticipationList $participationList;
    protected ParticipationListInterface $participationListInterface;
    protected PostResponse $postResponse;

    public function __construct(
        Response $response,
        DataObjectHelper $dataObjectHelper,
        ParticipationList $participationList,
        ParticipationListInterface $participationListInterface,
        PostResponse $postResponse,

    ) {
        $this->response = $response;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->participationList = $participationList;
        $this->participationListInterface = $participationListInterface;
        $this->postResponse = $postResponse;
    }

    public function send($httpCode, $dataArray, $type = null)
    {
        $this->response->setHttpResponseCode($httpCode);
        $resultObject = $this->_getResultObject($type);
        $this->dataObjectHelper->populateWithArray(
            $resultObject,
            $dataArray,
            $this->_getResultClass($type)
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

    protected function _getResultObject($type)
    {
        switch ($type) {
            case self::PARTICIPATIONS_FAILURE:
            case self::PARTICIPATIONS_LIST:
                return $this->participationList;

        }
    }

    protected function _getResultClass($type)
    {
        switch ($type) {
            case self::PARTICIPATIONS_FAILURE:
            case self::PARTICIPATIONS_LIST:
                return ParticipationListInterface::class;
        }
    }
}
