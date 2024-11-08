<?php

namespace Hiperdino\Anniversary2020\Api;

/**
 * Interface for managing Anniversary2020.
 * @api
 */
interface CustomerRgpdManagerInterface
{
    /**
     * Register rasca.
     * @param int $customerId
     * @return \Singular\EcommerceApp\Api\Data\PostResponseInterface
     */
    public function customerRgpd($customerId);
}
