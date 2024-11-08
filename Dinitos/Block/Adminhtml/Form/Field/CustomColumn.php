<?php

namespace Hiperdino\Dinitos\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Html\Select;

class CustomColumn extends Select
{
    protected mixed $groupdata;

    public function __construct(
        Context $context,
        GroupSourceLoggedInOnlyInterface $groupdata = null,
        array $data = []
    ) {
        $this->groupdata = $groupdata
            ?: ObjectManager::getInstance()->get(GroupSourceLoggedInOnlyInterface::class);
        parent::__construct($context, $data);
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value)
    {
        return $this->setId($value);
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    private function getSourceOptions()
    {
        return $this->groupdata->toOptionArray();
    }
}