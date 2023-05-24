<?php
namespace Mageplaza\GiftCard\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Mageplaza\GiftCard\Model\CustomerEntityFactory;

class Redeem extends \Magento\Framework\App\Action\Action
{
    protected $historyFactory;
    protected $giftcardFactory;
    protected $_redirect;
    protected $customerCurrent;
    protected $_customerFactory;

    public function __construct(
        \Mageplaza\GiftCard\Model\HistoryFactory $historyFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Mageplaza\GiftCard\Model\CustomerEntityFactory $customerFactory,
        \Magento\Customer\Model\SessionFactory $customerCurrent,
        Context $context,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    )
    {
        $this->historyFactory = $historyFactory;
        $this->giftcardFactory = $giftcardFactory;
        $this->_customerFactory = $customerFactory;
        $this->customerCurrent = $customerCurrent;
        parent::__construct($context);
        $this->_redirect = $redirect;
    }

    public function execute(){
        $this->updateRedeem();
        return $this->_redirect('giftcardfe/index/mygiftcard');
    }

    public function updateRedeem(){
        $param = $this->getRequest()->getParams();
        $giftcard_code = $this->getRequest()->getParam('code');
        $giftcard = $this->giftcardFactory->create();
        $history = $this->historyFactory->create();
        $customer = $this->_customerFactory->create();
        $customerId = $this->customerCurrent->create()->getCustomerId();

        $giftcard_id = $giftcard->load($giftcard_code,'code')->getId();
        $amount = $giftcard->getData('balance');
//        $history_action = $history->load($giftcard_id, 'giftcard_id')->getData('action');
//        $history_id = $history->load($giftcard_id, 'giftcard_id')->getId();
        if($giftcard->getData('balance')>0){
            if(isset($giftcard_id)){
                $data = [
                    'customer_id' => $customerId,
                    'giftcard_id' => $giftcard_id,
                    'action' => 'redeem',
                    'amount' => $amount
                ];

                $amountUsed_giftcard = $giftcard->load($giftcard_id,'giftcard_id')->getData('balance');
                $balance_giftcard = $giftcard->load($giftcard_id,'giftcard_id')->getData('balance') - $amountUsed_giftcard;
                $dataGiftcard = [
                    'giftcard_id' => $giftcard_id,
                    'balance' => $balance_giftcard,
                    'amount_used' => $amountUsed_giftcard
                ];


                $balance_customer = $customer->load($customerId,'entity_id')->getData('giftcard_balance') + $amountUsed_giftcard;
                $dataCustomer = [
                    'entity_id' => $customerId,
                    'giftcard_balance' => $balance_customer
                ];

                try{
                    $history->addData($data)->save();
                    $giftcard->setData($dataGiftcard)->save();
                    $customer->setData($dataCustomer)->save();
                }catch(\Exception $e){
                    $e->getMessage();
                }


                $this->messageManager->addSuccessMessage('Updated');
            }else{
                $this->messageManager->addErrorMessage('Code dont exist');
            }
        }else{
            $this->messageManager->addErrorMessage('Giftcard has expired');
        }
    }
}
