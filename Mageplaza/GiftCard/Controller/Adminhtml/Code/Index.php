<?php
    namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory = false;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::managecode::view';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if(!$this->_isAllowed()){
            $this->messageManager->addErrorMessage(__('You are not allowed to access this resource.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $resultPage->getConfig()->getTitle()->prepend((__('Gift Cards')));
        return $resultPage;
    }
}
