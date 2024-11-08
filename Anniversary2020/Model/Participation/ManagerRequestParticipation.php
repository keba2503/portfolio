<?php

namespace Hiperdino\Anniversary2020\Model\Participation;

use DateMalformedStringException;
use Exception;
use Hiperdino\Anniversary2020\Helper\Config;
use Hiperdino\Anniversary2020\Helper\Logger;
use Hiperdino\Anniversary2020\Helper\Queue;
use Hiperdino\Homai\Helper\Coupon;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Singular\Utils\Model\Store\NormalizeStoreCode;
use DateTime;

class ManagerRequestParticipation
{
    const TYPE_REQUEST_PARTICIPATION = 'request_participation';

    protected Queue $participationQueue;
    protected Config $helperConfig;

    protected Coupon $coupon;
    protected CustomerRepositoryInterface $customerRepository;
    protected Logger $logger;
    protected NormalizeStoreCode $normalizeStoreCode;

    public function __construct(
        Queue $participationQueue,
        Config $helperConfig,
        CustomerRepositoryInterface $customerRepository,
        Logger $logger,
        NormalizeStoreCode $normalizeStoreCode
    ) {
        $this->participationQueue = $participationQueue;
        $this->helperConfig = $helperConfig;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->normalizeStoreCode = $normalizeStoreCode;
    }

    /**
     * @throws DateMalformedStringException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute($order)
    {
        $customerId = $order->getData('customer_id');
        $customer = $this->customerRepository->getById($customerId);
        $customerAttribute = $customer->getCustomAttribute('sherpa_code');

        if ($customerAttribute) {
            $customerSherpaCodeValue = $customerAttribute->getValue();
        } else {
            $errorMessage = 'Sherpa code not available for the customer';
            throw new Exception($errorMessage);
        }

        $createdAt = $order->getData('created_at');
        $formattedDate = (new DateTime($createdAt))->format('Y-m-d');

        $requestBody = [
            'salesTicketCode' => $order->getData('increment_id'),
            'numberOfRaffleTickets' => $order->getData('anniversary_total_qty'),
            'salesDate' => $formattedDate,
            'salesStoreCode' => $this->normalizeStoreCode->normalizeStore($order->getStore()->getCode(), false, true),
            'salesClient' => $customerSherpaCodeValue,
            'amountSale' => $order->getSubtotal(),
        ];

        $requestBodyJson = json_encode($requestBody);
        $this->logger->logRequestParticipation(__('**Se encolan datos para solicitud de participaciones** :' . $requestBodyJson));

        $this->participationQueue->enqueue('', Queue::PENDING, $requestBodyJson, self::TYPE_REQUEST_PARTICIPATION, $customerId, '');
    }
}
