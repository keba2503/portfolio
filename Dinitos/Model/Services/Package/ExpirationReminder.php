<?php

namespace Hiperdino\Dinitos\Model\Services\Package;

use DateInterval;
use DateTime;
use Exception;
use Hiperdino\Dinitos\Helper\Config;
use Hiperdino\Dinitos\Helper\Logger;
use Hiperdino\Dinitos\Model\Data\Package;
use Hiperdino\Dinitos\Model\Repository\PackageRepository;
use Hiperdino\Dinitos\Model\Services\Alert\CreateExpirationAlert;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Singular\FlagManager\Model\Services\FlagManager;

class ExpirationReminder
{
    const REMINDER_QUEUE_FLAG = 'dinitos_expiration_reminder.flag';

    public function __construct(
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected Logger $logger,
        protected FlagManager $flagManager,
        protected Config $config,
        protected PackageRepository $packageRepository,
        protected CreateExpirationAlert $createExpirationAlertService
    ) {
    }

    public function process(): bool
    {
        try {
            $pushedIds = [];
            $notPushedIds = [];
            $this->logger->logDinitosReminder(__("Comenzamos recordatorios de dinitos"));
            if (!$this->flagManager->generateFlag(__(self::REMINDER_QUEUE_FLAG))) {
                $this->logger->logDinitosReminder(__("Hay otro proceso en ejecución"));

                return false;
            }
            $reminderConfig = $this->config->getDinitosReminderConfig();
            $daySendPeriod = [];
            foreach ($reminderConfig as $key => $value) {
                if ((int)$value === 0) {
                    continue;
                }
                $date = new DateTime('00:00:00');
                $initialDay = $date->add(new DateInterval("P{$value}D"))->format('Y-m-d H:i:s');
                $nextDay = $value + 1;
                $nextDate = new DateTime('00:00:00');
                $finalDay = $nextDate->add(new DateInterval("P{$nextDay}D"))->format('Y-m-d H:i:s');
                $daySendPeriod[$key] = ['initial_date' => $initialDay, 'final_date' => $finalDay];
            }
            $packagesToNotify = [];
            foreach ($daySendPeriod as $dayPeriod) {
                $this->searchCriteriaBuilder
                    ->addFilter('expiration_date', $dayPeriod['initial_date'], 'gteq')
                    ->addFilter('expiration_date', $dayPeriod['final_date'], 'lt')
                    ->addFilter('expired', 0)->addFilter('redeemed', 0);
                $packages = $this->packageRepository->getList($this->searchCriteriaBuilder->create())->getItems();
                if (count($packages)) {
                    $packagesToNotify[] = $packages;
                };
            }
            if (!count($packagesToNotify)) {
                $this->flagManager->removeFlag(self::REMINDER_QUEUE_FLAG);

                return true;
            }
            foreach ($packagesToNotify as $packagesForDay) {
                foreach ($packagesForDay as $package) {
                    /** @var Package $package */
                    if (array_key_exists($package->getId(), $pushedIds)) {
                        continue;
                    }
                    try {
                        $this->createExpirationAlertService->createAlert($package);
                        $pushedIds[] = $package->getId();
                    } catch (Exception $e) {
                        $notPushedIds[] = $package->getId();
                        continue;
                    }
                }
            }
            $pushedIds = implode(',', $pushedIds);
            $notPushedIds = implode(',', $notPushedIds);

        } catch (Exception $e) {
            $this->logger->logDinitosReminder(__("Error procesando los recordatorios:\n {$e->getMessage()}"));
            $this->flagManager->removeFlag(self::REMINDER_QUEUE_FLAG);

            return false;
        }
        if (!empty($pushedIds)) {
            $this->logger->logDinitosReminder(__("Pusheados recordatorios para los paquetes con id: {$pushedIds}"));
        }
        if (!empty($notPushedIds)) {
            $this->logger->logDinitosReminder(__("Error al pushear recordatorios para los paquetes con id: {$notPushedIds}"));
        }
        $this->flagManager->removeFlag(self::REMINDER_QUEUE_FLAG);
        $this->logger->logDinitosReminder(__("Terminamos recordatorios de dinitos"));

        return true;
    }
}