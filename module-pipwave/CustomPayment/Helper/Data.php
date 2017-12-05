<?php
namespace module-pipwave\CustomPayment\Helper;

//admin configuration data
class Data extends \Magento\framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\framework\App\Helper\Context $context,
        \Magento\framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    protected $scopeConfig;
    //from etc\adminhtml\system.xml
    const API_KEY = 'payment/custompayment/api_key';
    const API_SECRET = 'payment/custompayment/api_secret';
    const TEST_MODE = 'payment/custompayment/test_mode';
    const PROCESSING_FEE = 'payment/custompayment/processing_fee';
    const AUTO_SHIPPING = 'payment/custompayment/auto_shipping';
    const AUTO_INVOICE = 'payment/custompayment/auto_invoice';

    public function getApiKey() {
        return $this->scopeConfig->getValue(
            self::API_KEY,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiSecret() {
        return $this->scopeConfig->getValue(
            self::API_SECRET,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTestMode() {
        return $this->scopeConfig->getValue(
            self::TEST_MODE,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getProcessingFeeGroup()
    {
        return $this->scopeConfig->getValue(
            self::PROCESSING_FEE,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isShippingEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_SHIPPING,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isInvoiceEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_INVOICE,
            \Magento\module-store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}