<?php

namespace Hiperdino\Anniversary2020\Command;

use Exception;
use Hiperdino\Anniversary2020\Helper\QueueFactory;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanQueue extends Command
{
    protected State|string|null $state;
    protected QueueFactory $queue;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        QueueFactory $queue,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->queue = $queue;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:anniversary2020:clean_participation_queue");
        $this->setDescription("Limpia la cola de las participaciones");
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->getAreaCode();
        } catch (Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }
        $participationCleanQueueHelper = $this->queue->create();
        $participationCleanQueueHelper->cleanOldRegisters();
    }
}
