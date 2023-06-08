<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'history_id';
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\History', 'Mageplaza\GiftCard\Model\ResourceModel\History');
    }
    protected function _initSelect()
    {
        parent::_initSelect();

        $codeTable = $this->getTable('giftcard_code');
        $this->getSelect()
            ->joinLeft(
                ['code' => $codeTable],
                'main_table.giftcard_id = code.giftcard_id',
                ['giftcard_code' => 'code']
            );
    }
}
