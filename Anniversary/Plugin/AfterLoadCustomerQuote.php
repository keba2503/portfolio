<?php

namespace Hiperdino\Anniversary\Plugin;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Singular\EcommerceApp\Helper\Cart;

class AfterLoadCustomerQuote
{
    protected RedirectFactory $resultRedirectFactory;
    protected Session $session;
    protected Cart $ecommerceAppCartHelper;

    public function __construct(
        Context $context,
        Session $session,
        Cart $ecommerceAppCartHelper
    ) {
        $this->session = $session;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->ecommerceAppCartHelper = $ecommerceAppCartHelper;
    }

    public function afterLoadCustomerQuote($result)
    {
        $test = $this->session->getLoginCart();

        if ($test) {
            $this->session->unsLoginCart();
            $this->ecommerceAppCartHelper->getCart()->save();
        }

        return $result;
    }
}
