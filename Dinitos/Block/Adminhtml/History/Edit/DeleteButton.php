<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\History\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getTransactionId()) {
            $data = [
                'label' => __('Eliminar'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        '¿Está seguro de querer eliminar este movimiento?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getTransactionId()]);
    }
}
