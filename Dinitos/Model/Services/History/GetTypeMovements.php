<?php

namespace Hiperdino\Dinitos\Model\Services\History;

use Exception;

class GetTypeMovements
{
    const ACCUMULATION_MOVEMENT = 0;
    const REDEMPTION_MOVEMENT = 1;

    /**
     * @var array
     */
    protected $movementsType;

    public function __construct(
        array $movementsType = []
    ) {
        $this->movementsType = $movementsType;
    }

    /**
     * @throws Exception
     */
    public function getMovement($type): ?object
    {
        if (!isset($this->movementsType[$type])) {
            throw new Exception("No se reconoce ese tipo de movimiento");
        }

        return $this->movementsType[$type];
    }
}