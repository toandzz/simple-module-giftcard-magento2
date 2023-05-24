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
        $checkModule = $this->helperData->getEnable();
        if($checkModule == 1)
        {
            parent::collect($quote, $shippingAssignment, $total);
            $giftcode = $quote->getCouponCode();
            $baseDiscount = $this->_giftCardFactory->create()->load($giftcode,'code')->getData('balance');

            $total->setGiftCardDiscount($this->_priceCurrency->convert($baseDiscount));
            $subTotal = $total->getSubtotal();
            if($baseDiscount > $subTotal){
                $baseDiscount = $subTotal;
            }
            $discount = $this->_priceCurrency->convert($baseDiscount);

            $total->addTotalAmount('customdiscount', -$discount);
            $total->addBaseTotalAmount('customdiscount', -$baseDiscount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
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
