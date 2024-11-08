<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RewardListInterface extends CustomAttributesDataInterface
{
    const ITEMS = 'items';
    const MESSAGE = 'message';
    const CODE = 'code';

    /**
     * @return \Hiperdino\Dinitos\Api\Data\RewardInterface[]
     */
    public function getItems();

    /**
     * @param \Hiperdino\Dinitos\Api\Data\RewardInterface[] $items
     * @return $this
     */
    public function setItems($items);

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