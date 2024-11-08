<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Reward\Edit;

use Exception;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @throws Exception
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getDinitosRewardId()) {
            $data = [
                'label' => __('Eliminar'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        '¿Está seguro de querer eliminar esta recompensa?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20
            ];
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getDinitosRewardId()]);
    }
}
