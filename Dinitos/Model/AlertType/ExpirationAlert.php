<?php

namespace Hiperdino\Dinitos\Model\AlertType;

use Exception;
use Hiperdino\Alert\Api\Data\AlertInterface;
use Hiperdino\Alert\Api\Data\AlertRequestInterface;
use Hiperdino\Alert\Model\AlertType;
use Hiperdino\Alert\Model\AlertTypes\Generic;

class ExpirationAlert extends Generic
{
    const TYPE = 'dinitos_expiration';

    public function getAlertInfo(AlertRequestInterface $alertRequest)
    {
        $alertInfo = [];
        try {
            $alertInfo = [
                'customer_id' => $alertRequest->getCustomerCode(),
                'description' => $alertRequest->getMessage(),
                'extra_message' => $alertRequest->getExtraMessage() ?: "",
                'title' => $alertRequest->getTitle(),
                'pusheable' => $alertRequest->getPusheable(),
                'type_id' => $alertRequest->getType()
            ];
        } catch (Exception $e) {
        }

        return $alertInfo;
    }

    public function getPushData(AlertRequestInterface $alertRequest)
    {
        $pushData = [];
        try {
            $pushData = [
                'customer_id' => $alertRequest->getCustomerCode(),
                'message' => $alertRequest->getMessage(),
                'extra_params' => [
                    "action_type" => $this->getAlertType()->getActionType(),
                    "internal_link" => $this->getAlertType()->getAppUrl(),
                    "entity_id" => $alertRequest->getObjectId()
                ]
            ];
        } catch (Exception $e) {
            $this->alertLogger->logPushAlert($e->getMessage());
        }

        return $pushData;
    }

    public function getWebUrl(AlertInterface $alert)
    {
        return $this->url->getUrl($alert->getType()->getWebUrl());
    }

    public function getAppUrl(AlertInterface $alert): ?string
    {
        return $this->url->getUrl($alert->getType()->getAppUrl());
    }

    protected function getAlertType($code = self::TYPE): AlertType
    {
        return parent::getAlertType($code);
    }
}