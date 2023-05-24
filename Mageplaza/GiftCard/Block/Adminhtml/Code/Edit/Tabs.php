<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcard_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Information'));
    }
}
