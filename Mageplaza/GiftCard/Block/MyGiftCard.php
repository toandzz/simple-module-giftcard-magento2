<?php
namespace Mageplaza\GiftCard\Block;

use Magento\Catalog\Model\Config\Source\Price\Scope;
use Magento\Framework\App\ScopeInterface;

class MyGiftCard extends \Magento\Framework\View\Element\Template
{
    protected $_collectionFactory;
    protected $_giftcardFactory;
    protected $_currentCustomer;
    protected $_customerEntityFactory;
    protected $_storeManager;
    protected $_localeCurrency;
    protected $_helperData;
    protected $_timeZone;
    protected $config;

    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Model\ResourceModel\History\CollectionFactory $collectionFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Customer\Model\SessionFactory $currentCustomer,
        \Mageplaza\GiftCard\Model\CustomerEntityFactory $customerEntityFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
        \Magento\Sales\Model\Order\Config $config
    )
    {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->_giftcardFactory = $giftcardFactory;
        $this->_currentCustomer = $currentCustomer;
        $this->_customerEntityFactory = $customerEntityFactory;
        $this->_storeManager = $storeManager;
        $this->_localeCurrency = $localeCurrency;
        $this->_helperData = $helperData;
        $this->_timeZone = $timeZone;
        $this->config = $config;
    }

    public function getGiftCardBalance(){
        $customerId = $this->_currentCustomer->create()->getCustomer()->getId();
        $customer = $this->_customerEntityFactory->create();
        $balance = $customer->load($customerId,'entity_id')->getData('giftcard_balance');
        return $this->formatPrice($balance);
    }

    public function getValueRedeem(){
        $value = $this->_helperData->getAllowRedeem();
        return $value;
    }

    public function getHistory()
    {
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $limit = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        $customerId = $this->_currentCustomer->create()->getCustomer()->getId();
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId)->setOrder('action_time','DESC');
        $collection->setPageSize($limit);
        $collection->setCurPage($page);

        return $collection;
    }

    public function getUrlRedeem(){
        return $this->getUrl('giftcardfe/customer/redeem');
    }

    public function formatPrice($price){
        $store = $this->_storeManager->getStore();
        $sym = $store->getBaseCurrencyCode();
        $currency = $this->_localeCurrency->getCurrency($sym);
        $price = $currency->toCurrency(sprintf("%f", $price));
        return $price;
    }

    public function formatDateTime($time){
        $date = $this->_scopeConfig->getValue('catalog/custom_options/date_fields_order', ScopeInterface::SCOPE_DEFAULT);
        $format = str_replace(',','/', $date);
        $customFormat = str_replace(['d', 'm'], ['j', 'n'], $format);
        return $this->_timeZone->date(new \DateTime($time))->format($customFormat);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getHistory()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'giftcard.history.pager'
            )->setCollection(
                $this->getHistory()
            );
            $this->setChild('pager', $pager);
            $this->getHistory()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
