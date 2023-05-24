<?php


namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Mageplaza\GiftCard\Helper\Data;

class Info extends Generic implements TabInterface
{
    protected $_giftCardFactory;
    protected $_registry;
    protected $_redirect;
    protected $_response;
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $GiftCardFactory,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        Data $helper,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_giftCardFactory = $GiftCardFactory;
        $this->_redirect = $redirect;
        $this->_response = $response;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {
        $url_param = $this->getRequest()->getParams();
        $model = $this->_registry->registry('giftcard');
        $form = $this->_formFactory->create();
//        $this->setForm($form);
        $length = $this->_helper->getCodeLength();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Gift Card Information')]
        );
        $object_id = $model->getId();
        if (isset($url_param['giftcard_id']) && isset($object_id)) {
            $fieldset->addField(
                'giftcard_id',
                'hidden',
                [
                    'name' => 'giftcard_id',
//                    'value' => $model->getData('giftcard_id')
                ]
            );
            $fieldset->addField(
                'code',
                'text',
                [
                    'name' => 'code',
                    'label' => __('Code'),
//                    'value' => $model->getData('code'),
                    'readonly' => true,
                ]
            );

            $fieldset->addField(
                'balance',
                'text',
                [
                    'name' => 'balance',
                    'label' => __('Balance'),
//                    'value' => $model->getData('balance'),
                ]
            );

            $fieldset->addField(
                'created_from',
                'text',
                [
                    'name' => 'created_from',
                    'label' => __('Create From:'),
//                    'value' => $model->getData('created_from'),
                    'readonly' => true,
                ]
            );

//            var_dump($model->getData());
        } else if (isset($url_param['giftcard_id']) && !isset($object_id)) {
            $this->_redirect->redirect($this->_response, 'giftcard/code/index');
        } else {
            $fieldset->addField(
                'code',
                'text',
                [
                    'name' => 'code',
                    'label' => __('Code Length'),
                    'value' => $length
                ]
            );

            $fieldset->addField(
                'balance',
                'text',
                [
                    'name' => 'balance',
                    'label' => __('Balance'),
                    'value' => '',
                    'required' => true
                ]
            );
        }

        $form->addValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Gift Card Table');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
