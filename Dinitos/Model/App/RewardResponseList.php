<?php

namespace Hiperdino\Dinitos\Model\App;

use Hiperdino\Dinitos\Api\Data\RewardResponseListInterface;
use Magento\Framework\Model\AbstractModel;

class RewardResponseList extends AbstractModel implements RewardResponseListInterface
{
    public function getCustomerRewards()
    {
        return $this->getData(self::CUSTOMER_REWARDS);
    }

    public function setCustomerRewards($customerRewards): ?RewardResponseList
    {
        return $this->setData(self::CUSTOMER_REWARDS, $customerRewards);
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttributes(array $attributes)
    {
        // TODO: Implement setCustomAttributes() method.
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttribute($attributeCode)
    {
        // TODO: Implement getCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        // TODO: Implement setCustomAttribute() method.
    }

    /**
     * @inheritDoc
     */
    public function getCustomAttributes()
    {
        // TODO: Implement getCustomAttributes() method.
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code)
    {
        return $this->setData(self::CODE, $code);
    }
}