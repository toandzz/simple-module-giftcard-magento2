<?php
namespace Mageplaza\GiftCard\Plugin;

class GiftCardCode{
    protected $giftCardFactory;
    protected $helperData;
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->giftCardFactory = $giftCardFactory;
        $this->helperData =$helperData;
    }
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result){
        $quote = $this->checkoutSession->getQuote();
        $enableGiftcard = $this->helperData->getEnable();
        $giftcardCode = $quote->getCouponCode();
        $statusCode = $this->giftCardFactory->create()->load($giftcardCode,'code')->getData('status');
        if($enableGiftcard == 1 && $statusCode == 0){
            if($code = $giftcardCode) {
                return $code;
            }
        }
        return $result;
    }
}
