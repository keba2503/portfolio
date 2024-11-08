<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Reward\Edit;

use Exception;
use Hiperdino\Dinitos\Model\Repository\RewardRepository;
use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    protected Context $context;
    protected RewardRepository $dinitosRewardRepository;

    public function __construct(
        Context $context,
        RewardRepository $dinitosRewardRepository
    ) {
        $this->context = $context;
        $this->dinitosRewardRepository = $dinitosRewardRepository;
    }

    /**
     * @throws Exception
     */
    public function getDinitosRewardId(): ?string
    {
        $id = $this->context->getRequest()->getParam('id');
        $dinitosReward = $this->dinitosRewardRepository->getById($id);
        if ($dinitosReward->getId()) {
            return $dinitosReward->getId();
        }

        return null;
    }

    public function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
