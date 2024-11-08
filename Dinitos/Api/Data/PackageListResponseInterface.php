<?php

namespace Hiperdino\Dinitos\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface PackageListResponseInterface extends CustomAttributesDataInterface
{
    const PACKAGES = 'packages';
    const MESSAGE = 'message';
    const CODE = 'code';

    /**
     * @return \Hiperdino\Dinitos\Api\Data\PackageInterface
     */
    public function getPackages();

    /**
     * @param \Hiperdino\Dinitos\Api\Data\PackageInterface $packages
     * @return $this
     */
    public function setPackages($packages);

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