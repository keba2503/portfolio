<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Package\Edit;

use Exception;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @throws Exception
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getTransactionId()) {
            $data = [
                'label' => __('Eliminar'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        '¿Está seguro de querer eliminar este paquete?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20
            ];
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getTransactionId()]);
    }
}
