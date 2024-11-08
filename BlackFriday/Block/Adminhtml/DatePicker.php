<?php

namespace Hiperdino\BlackFriday\Block\Adminhtml;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        //check datepicker set or not
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
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