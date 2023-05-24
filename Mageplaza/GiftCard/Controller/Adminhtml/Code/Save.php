<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\RedirectFactory;
use RuntimeException;

class Save extends \Mageplaza\GiftCard\Controller\Adminhtml\InitGiftCard
{
    protected $_giftCardFactory;
    protected $registry;
    protected $resultRedirectFactory;
    protected $_helperData;
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::managecode::save';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        RedirectFactory $resultRedirect,
        Registry $registry
    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->registry = $registry;
        $this->_helperData = $helperData;
        $this->resultRedirectFactory = $resultRedirect;
        parent::__construct($context,$giftCardFactory,$registry);
    }

    public function execute(){
        if(!$this->_isAllowed()){
            $this->messageManager->addErrorMessage(__('You are not allowed to access this resource.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $param = $this->getRequest()->getParams();
        $giftcard = $this->initGiftCard();
        $resultRedirect = $this->resultRedirectFactory->create();
        if($giftcard){
            $this->prepareData($giftcard, $param);

            try{
                $giftcard->save();
                $this->messageManager->addSuccessMessage(__('You saved the gift card.'));

                if($this->getRequest()->getParam('back')){
                    $resultRedirect->setPath('*/*/edit', ['giftcard_id' => $giftcard->getId()]);
                    return $resultRedirect;
                }else{
                    $resultRedirect->setPath('*/*/index');
                    return $resultRedirect;
                }
            }catch(RuntimeException $e){
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            catch(\Exception $e){
                $this->messageManager->addExceptionMessage($e, __('This code already exist.'));
                return $this->_redirect('*/*/neww');
            }
        }
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    protected function prepareData($giftcard, $param = []){
        if(isset($param['giftcard_id'])){
            $data = [
                'balance' => $param['balance']
            ];
        }else{
            $code = $this->_helperData->randomCode($param['code']);
            $data = [
                'code' => $code,
                'balance' => $param['balance'],
                'created_from' => 'Admin',
                'amount_used' => '0'
            ];
        }
        $giftcard->setData($data);
    }
}
