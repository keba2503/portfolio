<?php

namespace Hiperdino\Anniversary2020\Api;

/**
 * @api
 */
interface ParticipationByCustomerManagerInterface
{
    /**
     * @param int $customerId
     * @return \Hiperdino\Anniversary2020\Api\Data\ParticipationListInterface
     */
    public function getParticipationByCustomer($customerId);
}