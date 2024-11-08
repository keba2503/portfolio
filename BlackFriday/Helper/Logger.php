<?php

namespace Hiperdino\BlackFriday\Helper;

class Logger extends \Singular\Logger\Model\Logger
{
    const GENERAL_LOG = 'blackfriday.log';
    const UPDATE_BLACKFRIDAY_LOG = 'update_blackfriday.log';

    /**
     * @param string $message
     * @param string $file
     */
    public function log($message, $file = self::GENERAL_LOG)
    {
        parent::log($message, $file);
    }

    public function logUpdateBlackfriday($message)
    {
        $this->log($message, self::UPDATE_BLACKFRIDAY_LOG);
    }
}
