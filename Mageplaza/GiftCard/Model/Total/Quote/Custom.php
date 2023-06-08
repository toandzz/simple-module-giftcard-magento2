<?php
namespace Mageplaza\GiftCard\Model\Total\Quote;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    protected $_priceCurrency;
    protected $_giftCardFactory;
    protected $helperData;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\GiftCard\Model\GiftCardFactory $_giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData
    ){
        $this->_priceCurrency = $priceCurrency;
        $this->_giftCardFactory = $_giftCardFactory;
        $this->helperData = $helperData;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        $checkModule = $this->helperData->getAllowUse();
        if($checkModule == 1)
        {
            parent::collect($quote, $shippingAssignment, $total);
            $giftcode = $quote->getGiftcardCode();
            $giftcard = $this->_giftCardFactory->create()->load($giftcode,'code');

            $amount = $giftcard->getData('amount_used');
            $baseDiscount = $giftcard->getData('balance') - $amount;

            $total->setGiftCardDiscount($this->_priceCurrency->convert($baseDiscount));
            $subTotal = $total->getSubtotal();
            if($baseDiscount > $subTotal){
                $baseDiscount = $subTotal;
            }
            $discount = $this->_priceCurrency->convert($baseDiscount);

            $total->addTotalAmount('customdiscount', -$discount);
            $total->addBaseTotalAmount('customdiscount', -$baseDiscount);
//            $total->setGrandTotal($total->getGrandTotal());
//            $total->setBaseGrandTotal($total->getBaseGrandTotal());
            $quote->setCustomDiscount(-$discount);
            $total->setCustomDiscount($discount);

            return $this;
        }
    }


    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = [
            'code' => 'customer_discount',
            'title' => __('Giftcard Discount (' . $total->getGiftCardDiscount(). ')'),
            'value' => $total->getCustomDiscount()
        ];
        return $result;
    }

}
