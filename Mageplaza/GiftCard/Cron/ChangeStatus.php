<?php
namespace Mageplaza\GiftCard\Cron;

class ChangeStatus
{
    protected $giftCardCollection;
    protected $giftCardFactory;
    public function __construct(
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollection,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
        $this->giftCardCollection = $giftCardCollection;
        $this->giftCardFactory = $giftCardFactory;
    }

    public function execute()
    {
       $giftcard = $this->giftCardFactory->create();
       $collection = $this->giftCardCollection->create();
       foreach ($collection as $item){
           if(strtotime($item->getCreatedAt()) + (60*60*24*14) - time() <= 0){
               $status = $giftcard->load($item->getGiftcardId(),'giftcard_id')->getData('status');
               if($status == 0){
                   $giftcard->setData(['giftcard_id'=> $item->getGiftcardId(),'status' => 1])->save();
               }
           }
       }
    }
}
