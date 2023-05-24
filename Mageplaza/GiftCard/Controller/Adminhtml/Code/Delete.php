<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $_giftcardFactory;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::managecode::delete';
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
        $this->_giftcardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if(!$this->_isAllowed()){
            $this->messageManager->addErrorMessage(__('You are not allowed to access this resource.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $giftcard = $this->_giftcardFactory->create();
        $param = $this->getRequest()->getParam('giftcard_id');
        $giftcard->load($param);
        if ($giftcard->getId()){
            $giftcard->delete();
        }
        $this->_redirect('giftcard/code/index');

    }
}
