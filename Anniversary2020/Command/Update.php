<?php

namespace Hiperdino\Anniversary2020\Command;

use Exception;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected State|string|null $state;
    protected RegisterParticipation $registerParticipation;
    protected Logger $log;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        RegisterParticipation $registerParticipation,
        Logger $log,
        $name = null
    ) {
        parent::__construct($name);

        $this->state = $state;
        $this->registerParticipation = $registerParticipation;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:anniversary2020:update");
        $this->setDescription("Para resetear los intentos fallidos de registro de rascas de los clientes");
        parent::configure();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->getAreaCode();
        } catch (Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }

        try {
            $this->registerParticipation->resetAllNumWrongRascasRegister();
            $this->log->log("Se han reseteado todos los intentos de registros de rascas");
        } catch (Exception $e) {

        }
    }
}
