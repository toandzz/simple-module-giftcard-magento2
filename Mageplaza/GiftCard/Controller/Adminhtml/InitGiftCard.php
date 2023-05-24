<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Model\GiftCardFactory;

abstract class InitGiftCard extends \Magento\Backend\App\Action
{
    public $giftCardFactory;

    public $_coreRegistry;

    public function __construct(
        Context $context,
        GiftCardFactory $giftCardFactory,
        Registry $coreRegistry
    )
    {
        $this->giftCardFactory = $giftCardFactory;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    protected function initGiftCard($register = false){
        $giftcardId = $this->getRequest()->getParam('id');

        $giftcard = $this->giftCardFactory->create();

        if($giftcardId){
                $giftcard->load($giftcardId);
                if(!$giftcard->getId()){
                    $this->messageManager->addErrorMessage(__('This gift card longer exists.'));
                }
                return false;
        }
        if($register){
            $this->_coreRegistry->register('giftcard', $giftcard);
        }

        return $giftcard;
    }
}
