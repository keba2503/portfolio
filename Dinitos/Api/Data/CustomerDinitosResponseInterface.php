<?php

namespace Hiperdino\Dinitos\Api\Data;

use Hiperdino\Dinitos\Model\App\CustomerDinitosResponse;
use Magento\Framework\Api\CustomAttributesDataInterface;

interface CustomerDinitosResponseInterface extends CustomAttributesDataInterface
{
    const DINITOS = "dinitos";
    const MESSAGE = 'message';
    const CODE = 'code';

    /**
     * @return ?int
     */
    public function getDinitos(): ?int;

    /**
     * @param $customerDinitos
     * @return CustomerDinitosResponse
     */
    public function setDinitos($customerDinitos): CustomerDinitosResponse;

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