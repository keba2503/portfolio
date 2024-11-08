<?php

namespace Hiperdino\Anniversary2020\Model\Data;

use Hiperdino\Anniversary2020\Api\Data\ParticipationListInterface;
use Magento\Framework\Api\AbstractExtensibleObject as AbstractExtensibleObject;

class ParticipationList extends AbstractExtensibleObject implements ParticipationListInterface
{
    /**
     * @inheritDoc
     */
    public function getParticipations()
    {
        return $this->_get(self::PARTICIPATIONS);
    }

    /**
     * @inheritDoc
     */
    public function setParticipations($participations)
    {
        return $this->setData(self::PARTICIPATIONS, $participations);
    }
}
