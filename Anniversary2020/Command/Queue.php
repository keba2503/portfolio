<?php

namespace Hiperdino\Anniversary2020\Command;

use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\QueueFactory;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Queue extends Command
{
    protected State|string|null $state;
    protected QueueFactory $queue;
    protected Config $config;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        QueueFactory $queue,
        Config $config,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->queue = $queue;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:anniversary2020:process_participation_queue");
        $this->setDescription("Procesa la cola de participaciones");
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

        $participationQueueHelper = $this->queue->create();

        if ($this->config->isAnniversaryEnabled()) {
            $participationQueueHelper->processParticipationQueue();
        }
    }
}
