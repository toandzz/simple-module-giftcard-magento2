<?php
namespace Mageplaza\GiftCard\Model\ResourceModel;


use Magento\Framework\Exception\LocalizedException;

class GiftCard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_giftcardFactory;
    protected $_messageManager;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        parent::__construct($context);
        $this->_giftcardFactory = $giftcardFactory;
        $this->_messageManager = $messageManager;
    }

    protected function _construct()
    {
        $this->_init('giftcard_code', 'giftcard_id');
    }
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object){
        $giftcard = $this->_giftcardFactory->create();
        $code = $object->getCode();
        $checkCode = $giftcard->load($code,'code')->getData('code');
        if($checkCode){
            throw new LocalizedException(__('This code already exist'));
        }
    }
}
