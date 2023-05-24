<?php

namespace Mageplaza\GiftCard\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_PATH_GIFTCARD = 'giftcard/';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_GIFTCARD . 'general/' . $code, $storeId);
    }

    public function getEnable(){
        return $this->getGeneralConfig('enable');
    }

    public function getAllowUse(){
        return $this->getGeneralConfig('allow_use');
    }

    public function getAllowRedeem(){
        return $this->getGeneralConfig('allow_redeem');
    }

    public function getCodeConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_GIFTCARD . 'general2/' . $code, $storeId);
    }

    public function getCodeLength(){
        return $this->getCodeConfig('code_length');
    }

    public function randomCode($length){
        $char = "ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789";
        $size = strlen($char);
        $code = "";
        for($i = 0; $i<$length; $i++){
            $code .= $char[rand(0, $size - 1)];
        }
        return $code;
    }
}
