<?php

namespace Hiperdino\Anniversary2020\Api\Data;

interface ParticipationListInterface
{
    const PARTICIPATIONS = 'participations';

    /**
     * @return \Hiperdino\Anniversary2020\Api\Data\ParticipationInterface[]
     */
    public function getParticipations();

    /**
     * @param \Hiperdino\Anniversary2020\Api\Data\ParticipationInterface[] $participations
     * @return $this
     */
    public function setParticipations($participations);
}
