<?php
namespace Mageplaza\GiftCard\Observer;

class BuyGiftCard implements \Magento\Framework\Event\ObserverInterface
{
    protected $product;
    protected $_giftcardFactory;
    protected $historyFactory;
    protected $helperData;
    protected $_messageManager;
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $product,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Mageplaza\GiftCard\Model\HistoryFactory $historyFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $message
    )
    {
        $this->product = $product;
        $this->_giftcardFactory = $giftcardFactory;
        $this->historyFactory = $historyFactory;
        $this->helperData = $helperData;
        $this->_messageManager = $message;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $codeLength = $this->helperData->getCodeLength();
        $order = $observer->getData('order');
        $_items = $order->getItems();
        $product = $this->product->create();
        $enable = $this->helperData->getEnable();
//        $this->_messageManager->addSuccessMessage($order->getIncrementId());
//        $this->_messageManager->addSuccessMessage($order->getCustomerId());
        foreach($_items as $item){
            $giftcard = $this->_giftcardFactory->create();
            $customerId = $order->getCustomerId();
            $incrementId = $order->getIncrementId();
            $giftcard_amount = $product->load($item->getProductId())->getGiftcardAmount();
            if($item->getProductType() == 'virtual' && $giftcard_amount > 0 && $enable == 1){
//                $this->_messageManager->addSuccessMessage('true');
                for($i = 0; $i < $item->getQtyOrdered(); $i++){
                    $code = $this->helperData->randomCode($codeLength);
                    $this->insertDataGiftCard($code, $giftcard_amount, $incrementId);

                    $giftcardId = $giftcard->load($code,'code')->getId();
                    $this->insertDataHistory($giftcardId, $customerId, $giftcard_amount, 'create',$incrementId);
                }
            }
        }
    }

    public function insertDataGiftCard($code, $amount, $incrementId)
    {
        $data = [
            'code' => $code,
            'balance' => $amount,
            'created_from' => $incrementId,
            'amount_used' => 0
        ];

        $giftcard = $this->_giftcardFactory->create();
            try{
                $giftcard->setData($data)->save();
            }catch(\Exception $e){
                $this->_messageManager->addErrorMessage('Failure');
            }
    }

    public function insertDataHistory($giftcardId, $customerId, $amount, $action, $incrementId)
    {
        $data = [
            'giftcard_id' => $giftcardId,
            'customer_id' => $customerId,
            'amount' => $amount,
            'action' => $action.' by order #'.$incrementId
        ];
        $history = $this->historyFactory->create();
        try{
            $history->setData($data)->save();
        }catch(\Exception $e){
            $this->_messageManager->addErrorMessage('Failure');
        }
    }

}
