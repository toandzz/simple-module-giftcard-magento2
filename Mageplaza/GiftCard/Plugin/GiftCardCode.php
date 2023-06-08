<?php
namespace Mageplaza\GiftCard\Plugin;

class GiftCardCode{
    protected $giftCardFactory;
    protected $helperData;
    protected $_messageManager;
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $_messageManager
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->giftCardFactory = $giftCardFactory;
        $this->helperData =$helperData;
        $this->_messageManager = $_messageManager;
    }
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result){
        $quote = $this->checkoutSession->getQuote();
        $enableGiftcard = $this->helperData->getEnable();
        $giftcardCode = $quote->getGiftcardCode();
        $statusCode = $this->giftCardFactory->create()->load($giftcardCode,'code')->getData('status');
        if($enableGiftcard == 1){
            if($statusCode == 0){
                if($code = $giftcardCode) {
                    return $code;
                }
            }else{
                $quote->setGiftcardCode('')->save();
                $this->_messageManager->addErrorMessage('Gift card has been locked because it has expired');
            }
        }
        return $result;
    }
}
