<?php

namespace Hiperdino\Anniversary2020\Model\Service;

use Hiperdino\Anniversary2020\Helper\Config;
use Singular\PushNotifications\Helper\Notifications;
use Singular\PushNotifications\Model\Config\Source\Actions;

class Pusher
{
    protected Config $config;
    protected Notifications $notifications;

    public function __construct(
        Config $config,
        Notifications $notifications
    ) {
        $this->config = $config;
        $this->notifications = $notifications;
    }

    public function sendCustomerParticipationPush($customerId)
    {
        $message = $this->config->getPushParticipationMessage();

        $data = [
            "action_type" => Actions::TYPE_INTERNAL_LINK,
            "internal_link" => $this->config->getPushParticipationInternalPath()
        ];

        $this->notifications->sendPushNotificationForCustomer($customerId, $message, $data);
    }

}
