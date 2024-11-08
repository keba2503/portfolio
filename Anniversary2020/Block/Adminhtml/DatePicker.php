<?php

namespace Hiperdino\Anniversary2020\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DatePicker extends Field
{
    protected Registry $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();

        if (!$this->coreRegistry->registry('datepicker_loaded')) {
            $this->coreRegistry->registry('datepicker_loaded', 1);
        }
        $html .= '<button name="datepicker" id="button-' . $element->getHtmlId() . '" type="button" style="display:none;" class="ui-datepicker-trigger '
            . 'v-middle"><span>Seleccionar fecha</span></button>';

        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "yy/mm/dd" } );
                    jq("#button-' . $element->getHtmlId() . '.ui-datepicker-trigger").removeAttr("style");
                    jq("#button-' . $element->getHtmlId() . '.ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';

        return $html;
    }
}