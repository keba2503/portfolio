<?php

namespace Hiperdino\Anniversary\Helper;

class Logger extends \Singular\Logger\Model\Logger
{
    const GENERAL_LOG = 'anniversary.log';
    const UPDATE_ANNIVERSARY_LOG = 'update_anniversary.log';

    /**
     * @param string $message
     * @param string $file
     */
    public function log($message, $file = self::GENERAL_LOG)
    {
        parent::log($message, $file);
    }

    public function logUpdateAnniversary($message)
    {
        $this->log($message, self::UPDATE_ANNIVERSARY_LOG);
    }
}
