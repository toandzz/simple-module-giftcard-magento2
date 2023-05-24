<?php


namespace Mageplaza\GiftCard\Block\Adminhtml\Code;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry           $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array                                 $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $id = $this->getRequest()->getParam('giftcard_id');
        if($id){
            $this->_objectId = 'giftcard_id';
            $this->_blockGroup = 'Mageplaza_GiftCard';
            $this->_controller = 'adminhtml_code';
            $this->buttonList->update('save', 'label', __('Save Gift Card'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ]
                        ]
                    ]
                ],
                -100
            );
            $this->addButton(
                'delete',
                [
                    'label' => __('Delete'),
                    'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                        . ','
                        . json_encode($this->getDeleteUrl()
                        )
                        . ')',
                    'class' => 'scalable delete',
                    'level' => -1
                ]
            );
        }else{
            $this->_objectId = 'giftcard_id';
            $this->_blockGroup = 'Mageplaza_GiftCard';
            $this->_controller = 'adminhtml_code';
            $this->buttonList->update('save', 'label', __('Save Gift Card'));
        }

        parent::_construct();
    }
}
