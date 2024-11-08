<?php

namespace Hiperdino\Dinitos\Command;

use Exception;
use Hiperdino\Dinitos\Model\Services\Package\ExpirationReminder;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reminder extends Command
{
    public function __construct(
        protected State $state,
        protected ExpirationReminder $expirationReminder,
        protected $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName("hiperdino:dinitos:reminder");
        $this->setDescription("Procesa los recordatorios de los dinitos próximos a expirar.");
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->state->getAreaCode();
        } catch (Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }
        $this->expirationReminder->process();
    }
}