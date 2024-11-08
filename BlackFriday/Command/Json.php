<?php

namespace Hiperdino\BlackFriday\Command;

use Hiperdino\BlackFriday\Helper\Json as JsonHelper;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Json extends Command {

	protected $_state;
	protected $jsonHelper;

	/**
	 * @inheritdoc
	 */
	public function __construct(
		State $state,
		JsonHelper $jsonHelper,
		$name = null
	) {
		$this->_state = $state;
		$this->jsonHelper = $jsonHelper;
		parent::__construct($name);
	}

	/**
	 * @inheritdoc
	 */
	protected function configure() {
		$this->setName("hiperdino:blackfriday:json");
		$this->setDescription("Actualiza el JSON de tiendas Black Friday.");
		parent::configure();
	}

	/**
	 * @inheritdoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
			$this->_state->getAreaCode();
		} catch (\Exception $e) {
			$this->_state->setAreaCode('adminhtml');
		}

		$this->jsonHelper->updateStoresJson();
	}
}
