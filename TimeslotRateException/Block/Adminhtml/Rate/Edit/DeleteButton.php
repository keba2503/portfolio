<?php

namespace Hiperdino\TimeslotRateException\Block\Adminhtml\Rate\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ($this->getTransactionId()) {
            $data = [
                'label' => __('Eliminar'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        '¿Está seguro de querer eliminar esta tarifa?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
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
