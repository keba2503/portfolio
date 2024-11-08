<?php

namespace Hiperdino\Anniversary2020\Helper;

class Logger extends \Singular\Logger\Model\Logger
{
    const GENERAL_LOG = 'anniversary2020.log';
    const RAFFLE_SERVICE_LOG = 'participation_raffle_service.log';
    const PARTICIPATION_BY_CUSTOMER_LOG = 'participation_by_customer.log';
    const PARTICIPATION_SCRATCH_LOG = 'participation_scratch.log';
    const REGISTER_PARTICIPATION_LOG = 'participation_register.log';
    const REGISTER_CUSTOMER_RGPD_LOG = 'participation_customer_register_rgpd.log';
    const ASSIGN_CUSTOMER_PARTICIPATION = 'participation_assign_customer.log';
    const REQUETS_PARTICIPATION = 'participation_request.log';

    /**
     * @param string $message
     * @param string $file
     */
    public function log($message, $file = self::GENERAL_LOG)
    {
        parent::log($message, $file);
    }

    /**
     * @param string $message
     */
    public function logComerzziaEndpoint($message)
    {
        $this->log($message, self::RAFFLE_SERVICE_LOG);
    }

    /**
     * @param string $message
     */
    public function logParticipationByCustomer($message)
    {
        $this->log($message, self::PARTICIPATION_BY_CUSTOMER_LOG);
    }

    /**
     * @param string $message
     */
    public function logRegisterParticipation($message)
    {
        $this->log($message, self::REGISTER_PARTICIPATION_LOG);
    }

    /**
     * @param string $message
     */
    public function logRegisterCustomeRgpd($message)
    {
        $this->log($message, self::REGISTER_CUSTOMER_RGPD_LOG);
    }

    /**
     * @param string $message
     */
    public function logParticipationScratch($message)
    {
        $this->log($message, self::PARTICIPATION_SCRATCH_LOG);
    }

    /**
     * @param string $message
     */
    public function logAssignParticipationByCustomer($message)
    {
        $this->log($message, self::ASSIGN_CUSTOMER_PARTICIPATION);
    }

    /**
     * @param string $message
     */
    public function logRequestParticipation($message)
    {
        $this->log($message, self::REQUETS_PARTICIPATION);
    }
}
