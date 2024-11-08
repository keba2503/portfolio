<?php

namespace Hiperdino\Anniversary2020\Ui\Component\Listing\Column;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    const GET_PARTICIPATION = 'get_participation';
    const SCRATCH_PARTICIPATION = 'scratch_participation';
    const REGISTER_PARTICIPATION = 'register_raffle';
    const ASSOCIATED_CUSTOMER = 'associated_customer';
    const REQUEST_PARTICIPATION = 'request_participation';
    const PARTICIPATION_BY_CUSTOMER = 'participation_by_customer';


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GET_PARTICIPATION, 'label' => __('Consulta de participación por ID')],
            ['value' => self::SCRATCH_PARTICIPATION, 'label' => __('Rascado de participaciones')],
            ['value' => self::REGISTER_PARTICIPATION, 'label' => __('Registro de participaciones')],
            ['value' => self::ASSOCIATED_CUSTOMER, 'label' => __('Asociar participación a cliente')],
            ['value' => self::REQUEST_PARTICIPATION, 'label' => __('Solicitud de participaciones')],
            ['value' => self::PARTICIPATION_BY_CUSTOMER, 'label' => __('Participaciones por cliente')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::GET_PARTICIPATION => __('Consulta de participación por ID'),
            self::SCRATCH_PARTICIPATION => __('Rascado de participaciones'),
            self::REGISTER_PARTICIPATION => __('Registro de participaciones'),
            self::ASSOCIATED_CUSTOMER => __('Asociar participación a cliente'),
            self::REQUEST_PARTICIPATION => __('Solicitud de participaciones'),
            self::PARTICIPATION_BY_CUSTOMER => __('Participaciones por cliente'),
        ];
    }
}
