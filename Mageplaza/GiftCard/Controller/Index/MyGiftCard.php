<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Context;

class MyGiftCard extends \Magento\Framework\App\Action\Action {
    protected $helperData;

    public function __construct(
        \Mageplaza\GiftCard\Helper\Data $helperData,
        Context $context
    )
    {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function execute() {
        $enable = $this->helperData->getEnable();
        if($enable == 1){
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        }else{
            $this->_redirect('customer/account/');
        }

    }
}

