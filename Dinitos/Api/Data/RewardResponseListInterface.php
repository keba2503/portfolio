<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RewardResponseListInterface extends CustomAttributesDataInterface
{
    const CUSTOMER_REWARDS = 'customer_rewards';
    const MESSAGE = 'message';
    const CODE = 'code';

    /**
     * @return \Hiperdino\Dinitos\Api\Data\RewardResponseInterface[]
     */
    public function getCustomerRewards();

    /**
     * @param \Hiperdino\Dinitos\Api\Data\RewardResponseInterface[] $customerRewards
     * @return $this
     */
    public function setCustomerRewards($customerRewards);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     * @return string
     */
    public function setMessage(string $message);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return string
     */
    public function setCode(string $code);
}