<?php
namespace Mageplaza\GiftCard\Observer;

class CheckCode implements \Magento\Framework\Event\ObserverInterface
{
    protected $giftcardFactory;
    protected $_messageManager;
    protected $_redirect;
    protected $actionFlag;
    protected $_checkoutSession;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Framework\Message\ManagerInterface $_messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->giftcardFactory = $giftCardFactory;
        $this->_messageManager = $_messageManager;
        $this->_redirect = $redirect;
        $this->actionFlag = $actionFlag;
        $this->_checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $code = $controller->getRequest()->getParam('coupon_code');
        // Kiêm tra xem co code trong params khong
        if($code){
            $giftcard_model = $this->giftcardFactory->create();
            $giftcard_id = $giftcard_model->load($code,'code')->getId();
            $giftcard_balance = $giftcard_model->load($code,'code')->getData('balance');
            if ($giftcard_id) {
                if($giftcard_balance > 0){
                    $this->_messageManager->addSuccessMessage('Gift code applied successfully');
                    $quote = $this->_checkoutSession->getQuote();
                    $quote->setCouponCode($code);
                    $quote->save();
                    // case nay se dua ve luong su ly cua gc code
                }else{
                    $this->_messageManager->addErrorMessage('This giftcode has expired');
                }
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->_redirect->redirect($controller->getResponse(),'checkout/cart');
            }
            // case nay se dua ve luong su ly cua core
        }
        if ($controller->getRequest()->getParam('remove')) {
            $this->_checkoutSession->getQuote()->setCouponCode('')->save();
            // case nay se dua ve luong su ly cua core
        }
        // case nay se dua ve luong su ly cua core
    }
}

