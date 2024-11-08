<?php

namespace Hiperdino\BlackFriday\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command {

    protected $_state;
    /**
     * @var \Hiperdino\BlackFriday\Helper\Update
     */
    protected $blackFridayUpdate;

    /**
     * @inheritdoc
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Hiperdino\BlackFriday\Helper\Update $blackFridayUpdate,
        $name = null
    ) {
        $this->_state = $state;
        $this->blackFridayUpdate = $blackFridayUpdate;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:blackfriday:update");
        $this->setDescription("Actualiza los tags de Black Friday de los productos.");
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->_state->getAreaCode();
        } catch (\Exception $e) {
            $this->_state->setAreaCode('adminhtml');
        }

        try{

            $this->blackFridayUpdate->processUpdate();

        } catch (\Exception $e) {
            $output->print_r($e->getMessage());
            $output->print_r($e->getTraceAsString());
        }
    }
}
