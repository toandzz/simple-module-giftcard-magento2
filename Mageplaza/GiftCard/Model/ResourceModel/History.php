<?php
namespace Mageplaza\GiftCard\Model\ResourceModel;


use Magento\Framework\Exception\LocalizedException;

class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('giftcard_history', 'history_id');
    }

}
