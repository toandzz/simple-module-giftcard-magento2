<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;

class actionDelete extends Action
{
    protected $_giftcardFactory;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::managecode::massdelete';
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
        $param_selector = $this->getRequest()->getParam('selected');
        $giftcard_model = $this->_giftcardFactory->create();

        for ($i = 0; $i < count($param_selector); $i++) {
            $giftcard_model->load($param_selector[$i])->delete();
        }
        $this->_redirect('giftcard/code/index');
    }
}
