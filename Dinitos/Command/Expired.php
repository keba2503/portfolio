<?php

namespace Hiperdino\Dinitos\Command;

use Exception;
use Hiperdino\Dinitos\Model\Services\Package\Expired as ExpiredPackage;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Expired extends Command
{

    protected State $state;
    protected ExpiredPackage $expiredPackage;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        ExpiredPackage $expiredPackage,
        $name = null
    ) {
        $this->state = $state;
        $this->expiredPackage = $expiredPackage;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName("hiperdino:dinitos:expired");
        $this->setDescription("Cambia el estado de los dinitos expirados y realiza el movimiento de caducidad.");
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->state->getAreaCode();
        } catch (Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }

        $this->expiredPackage->processPackageExpiration();
    }
}
