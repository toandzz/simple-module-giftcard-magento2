<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class Neww extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory = false;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::managecode::new';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    )
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        if(!$this->_isAllowed()){
            $this->messageManager->addErrorMessage(__('You are not allowed to access this resource.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
            $resultForwardFactory = $this->resultForwardFactory->create();
            $this->_forward('edit');

            return $resultForwardFactory;

    }
}
