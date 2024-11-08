<?php

namespace Hiperdino\Dinitos\Helper;

class Logger extends \Hiperdino\Checkout\Helper\Logger
{
    const GENERAL_LOG = "dinitos.log";
    const UPDATE_DINITOS_LOG = "update_dinitos.log";
    const DINITOS_REWARD_LOG = "dinitos_rewards.log";
    const HISTORY_DINITOS_LOG = "history_dinitos.log";
    const DINITOS_SERVICE_LOG = "dinitos_service.log";
    const DINITOS_CUSTOMER_LOG = "dinitos_customer.log";
    const DINITOS_MOVEMENT_LOG = "dinitos_movement.log";
    const DINITOS_PACKAGES_LOG = "dinitos_packages.log";
    const DINITOS_APP_LOG = "dinitos_app.log";
    const DINITOS_REMINDER_LOG = "dinitos_reminder.log";
    const DINITOS_REWARDS_INVOICE_COLLECT_TOTALS = "dinitos_rewards_invoice_collect_totals.log";
    const FILTER_QUOTE_REWARDS_LOG = "filter_quote_rewards.log";

    public function logUpdateDinitos($message): void
    {
        $this->log($message, self::UPDATE_DINITOS_LOG);
    }

    public function logDinitosReward($message): void
    {
        $this->log($message, self::DINITOS_REWARD_LOG);
    }

    public function logDinitosService($message): void
    {
        $this->log($message, self::DINITOS_SERVICE_LOG);
    }

    public function logDinitosCustomer($message): void
    {
        $this->log($message, self::DINITOS_CUSTOMER_LOG);
    }

    public function logDinitosMovement($message): void
    {
        $this->log($message, self::DINITOS_MOVEMENT_LOG);
    }

    public function logHistoryDinitos($message): void
    {
        $this->log($message, self::HISTORY_DINITOS_LOG);
    }

    public function logPackages($message): void
    {
        $this->log($message, self::DINITOS_PACKAGES_LOG);
    }

    public function logDinitosApp($message): void
    {
        $this->log($message, self::DINITOS_APP_LOG);
    }

    public function logDinitosReminder($message): void
    {
        $this->log($message, self::DINITOS_REMINDER_LOG);
    }

    public function logDinitosRewardsInvoiceTotals($message): void
    {
        $this->log($message, self::DINITOS_REWARDS_INVOICE_COLLECT_TOTALS);
    }

    public function logFilterDinitosReward($message): void
    {
        $this->log($message, self::FILTER_QUOTE_REWARDS_LOG);
    }
}