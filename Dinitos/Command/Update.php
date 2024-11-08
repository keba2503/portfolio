<?php

namespace Hiperdino\Dinitos\Command;

use Exception;
use Hiperdino\Dinitos\Helper\Update as UpdateHelper;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command {

    protected State $state;
    protected UpdateHelper $dinitosUpdate;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        UpdateHelper $dinitosUpdate,
        $name = null
    ) {
        $this->state = $state;
        $this->dinitosUpdate = $dinitosUpdate;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName("hiperdino:dinitos:update");
        $this->setDescription("Actualiza los dinitos de los productos.");
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

        $this->dinitosUpdate->processUpdate();
    }
}
