<?php

namespace Hiperdino\Dinitos\Cron;

use Hiperdino\Dinitos\Helper\Update;

class QueueDinitosUpdate
{
    protected Update $dinitosUpdate;

    public function __construct(
        Update $dinitosUpdate
    ) {
        $this->dinitosUpdate = $dinitosUpdate;
    }

    public function execute()
    {
        $this->dinitosUpdate->processUpdate();

        return $this;
    }
}
