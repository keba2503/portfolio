<?php

namespace Hiperdino\Anniversary2020\Observer;

use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Model\Service\RegisterParticipation;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    protected RegisterParticipation $registerParticipation;
    protected Session $customerSession;
    protected Config $helperConfig;

    /**
     * @param Session $customerSession
     * @param Config $helperConfig
     * @param RegisterParticipation $registerParticipation
     */
    public function __construct(
        Session $customerSession,
        Config $helperConfig,
        RegisterParticipation $registerParticipation
    ) {
        $this->customerSession = $customerSession;
        $this->helperConfig = $helperConfig;
        $this->registerParticipation = $registerParticipation;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperConfig->isAnniversaryEnabled()) {
            return $this;
        }

        $rascaCode = isset($_POST[RegisterParticipation::COOKIE_RASCA_NAME]) ? trim($_POST[RegisterParticipation::COOKIE_RASCA_NAME]) : '';
        if ($rascaCode) {
            $this->customerSession->setData(RegisterParticipation::SESSION_RASCA_NAME, $rascaCode);
        }

        return $this;
    }
}
