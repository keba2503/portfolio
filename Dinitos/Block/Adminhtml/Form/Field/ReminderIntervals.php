<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class ReminderIntervals extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('send_interval', ['label' => __('Recordatorio (días)'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Nuevo');
    }
}