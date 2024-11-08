<?php

namespace Hiperdino\Dinitos\Block\Adminhtml;

use Hiperdino\Dinitos\Block\Adminhtml\Form\Field\CustomColumn;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class DynamicField extends AbstractFieldArray
{
    protected $dropdownRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn(
            'title_native',
            [
                'label' => __('Nombre interno'),
                'renderer' => $this->getRenderer(),
                'class' => 'required-entry'
            ]
        );
        $this->addColumn(
            'title_custom',
            [
                'label' => __('Nombre personalizado'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    private function getRenderer()
    {
        if (!$this->dropdownRenderer) {
            $this->dropdownRenderer = $this->getLayout()->createBlock(
                CustomColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->dropdownRenderer->setOptions($this->_getOptions());
        }

        return $this->dropdownRenderer;
    }

    private function _getOptions()
    {
        return [
            'position' => __('Defecto (Posición)'),
            'obtained' => __('Obtenidos'),
            'redeemed' => __('Canjeados'),
            'expired' => __('Caducados'),
            'refunded' => __('Reembolsados'),
            'deducted' => __('Deducidos')
        ];
    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $dropdownField = $row->getData('title_native');

        if ($dropdownField !== null) {
            $options['option_' . $this->getRenderer()->calcOptionHash($dropdownField)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
