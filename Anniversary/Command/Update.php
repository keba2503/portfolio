<?php

namespace Hiperdino\Anniversary\Command;

use Hiperdino\Anniversary\Helper\Update as HelperUpdate;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected State|string|null $state;
    protected HelperUpdate $anniversaryUpdate;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        HelperUpdate $anniversaryUpdate,
        $name = null
    ) {
        $this->state = $state;
        $this->anniversaryUpdate = $anniversaryUpdate;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:anniversary:update");
        $this->setDescription("Actualiza los tags de Rasca y Gana de los productos.");
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
        } catch (\Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }

        $this->anniversaryUpdate->processUpdate();
    }
}
