<?php

namespace Hiperdino\Anniversary2020\Command;

use Exception;
use Hiperdino\Anniversary2020\Model\Service\ParticipationWinnerEmail;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailTest extends Command
{
    protected State|string|null $state;
    protected ParticipationWinnerEmail $participationWinnerEmail;
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @inheritdoc
     */
    public function __construct(
        State $state,
        ParticipationWinnerEmail $participationWinnerEmail,
        CustomerRepositoryInterface $customerRepository,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->participationWinnerEmail = $participationWinnerEmail;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("hiperdino:anniversary2020:send_email_test");
        $this->setDescription("Envía email de prueba");
        $this->addArgument('customer_id', InputArgument::REQUIRED, 'Customer Id');
        $this->addArgument('img_name', InputArgument::REQUIRED, 'Image Name');

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
        try {
            $customerId = $input->getArgument('customer_id');
            $imageName = $input->getArgument('img_name');
            $customer = $this->customerRepository->getById($customerId);
            $this->participationWinnerEmail->sendEmail($customer, "DDP3NF", "Osito de Peluche", date('d-m-Y H:i:s'), $imageName);
        } catch (Exception $e) {
            $output->writeln("No se ha podido enviar el email: ".$e->getMessage());
        }
    }
}
