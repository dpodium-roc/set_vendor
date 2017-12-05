<?php
namespace module-pipwave\CustomPayment\Block;

class InformationNeeded extends \Magento\module-checkout\Block\Onepage
{
    //object/class
    protected $customer;
    protected $checkout;
    protected $creditmemoFactory;
    protected $CreditmemoService;
    protected $productMetadata;
    protected $_storeManager;
    protected $adminConfig;
    protected $urlLink;
    protected $invoice;
    protected $shipment;

    //variables
    protected $data;
    protected $url;
    protected $renderUrl;
    protected $loadingImageUrl;
    protected $testMode;
    protected $version;
    protected $signatureParam;

    public function __construct(
        \Magento\module-store\Model\StoreManagerInterface $storeManager,
        \Magento\module-customer\Model\Session $customer,
        \Magento\module-checkout\Model\Session $checkout,
        \Magento\module-sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\module-sales\Model\Service\CreditmemoService $CreditmemoService,
        \Magento\framework\App\ProductMetadataInterface $productMetadata,
        \module-pipwave\CustomPayment\Helper\Data $adminData,
        \module-pipwave\CustomPayment\Model\Url $urlLink,
        \module-pipwave\CustomPayment\Model\Order\Invoice $invoice,
        \module-pipwave\CustomPayment\Model\Order\Shipment $shipment
    ) {
        $this->_storeManager = $storeManager;
        $this->customer = $customer;
        $this->checkout = $checkout;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->CreditmemoService = $CreditmemoService;
        $this->productMetadata = $productMetadata;
        $this->adminConfig = $adminData;
        $this->urlLink = $urlLink;
        $this->invoice = $invoice;
        $this->shipment = $shipment;
    }

    //called in module-pipwave\CustomPayment\Controller\Index\Index
    function prepareData() {
        self::settData();
        self::setSignatureParam();
    }

    //called in SELF
    function settData() {
        $order = $this->checkout->getLastRealOrder();

        $total = $order->getGrandTotal();

        //add ngrok url to replace 'localhost'
        $notificationUrl = 'https://b7407878.ngrok.io/magento2-develop/magento2-develop/notification/notification/index';

        //ship address
        $shipAddress1 = '';
        $shipAddress2 = '';
        if ($order->getShippingAddress()->getStreet()!=null) {
            $shipAddress1 = implode(' ', $order->getShippingAddress()->getStreet());
        }

        //bill address
        $billAddress1 = '';
        $billAddress2 = '';
        if ($order->getBillingAddress()->getStreet()!=null) {
            $billAddress1 = implode(' ', $order->getBillingAddress()->getStreet());
        }

        $this->data = array(
            'action' => 'initiate-payment', 
            'timestamp' => time(), 
            'api_key' => $this->adminConfig->getApiKey(), 
            'api_secret' => $this->adminConfig->getApiSecret(), 
            'txn_id' => $order->getIncrementId(),
            'amount' => round($total, 2), 
            'currency_code' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(), 
            'shipping_amount' => $order->getShippingAmount(), 
            'buyer_info' => array(
                'id' => $this->customer->getCustomerId(), 
                'email' => $this->customer->getCustomer()->getEmail(), 
                'first_name' => $order->getBillingAddress()->getFirstname(), 
                'last_name' => $order->getBillingAddress()->getLastname(), 
                'contact_no' => $order->getBillingAddress()->getTelephone(), 
                'country_code' => $order->getBillingAddress()->getCountryId(), 
                'surcharge_group' => $this->adminConfig->getProcessingFeeGroup(), 
            ), 
            'shipping_info' => array(
                'name' => $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname(), 
                'city' => $order->getShippingAddress()->getCity(), 
                'zip' => $order->getShippingAddress()->getPostCode(), 
                'country_iso2' => $order->getShippingAddress()->getCountryId(), 
                'email' => $order->getShippingAddress()->getEmail(), 
                'contact_no' => $order->getShippingAddress()->getTelephone(), 
                'address1' => $shipAddress1, 
                //'address2' => $shipAddress2,
                'state' => $order->getShippingAddress()->getRegion(), 
            ), 
            'billing_info' => array(
                'name' => $order->getBillingAddress()->getFirstname() . ' ' . $order->getBillingAddress()->getLastname(), 
                'city' => $order->getBillingAddress()->getCity(), 
                'zip' => $order->getBillingAddress()->getPostCode(), 
                'country_iso2' => $order->getBillingAddress()->getCountryId(), 
                'email' => $order->getBillingAddress()->getEmail(), 
                'contact_no' => $order->getBillingAddress()->getTelephone(), 
                'address1' => $billAddress1, 
                //'address2' => $billAddress2,
                'state' => $order->getBillingAddress()->getRegion(), 
            ), 
            'api_override' => array(
                'success_url' => $this->urlLink->defaultSuccessPageUrl(), 
                'fail_url' => $this->urlLink->defaultFailPageUrl(), 
                'notification_url' => /*$this->urlLink->notificationPageUrl(), */ $notificationUrl, 
            ), 
        );

        $itemInfo = array();
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();

            // some weird things came out (repetition) if without if else
            if ((float)$product->getPrice()!=0) {
            $itemInfo[] = array(
                'name' => (null!==$product->getName() && !empty($product->getName()) ? $product->getName() : ''),
                'sku' => (null!==$product->getSku() && !empty($product->getSku()) ? $product->getSku() : ''),
                'currency_code' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                'amount' => (float)$product->getPrice(),
                'quantity' => (int)$item->getQtyOrdered(),
                );
            }
        }
        if (count($itemInfo) > 0) {
            $this->data['item_info'] = $itemInfo;
        }

        $this->testMode = $this->adminConfig->getTestMode();
        $this->url = $this->urlLink->getUrl($this->testMode);
        $this->renderUrl = $this->urlLink->getRenderUrl($this->testMode);
        $this->loadingImageUrl = $this->urlLink->getLoadingImageUrl($this->testMode);
    }

    function setSignatureParam() {
        //need modification, call object manager?
        //read some_functions_get_information.php [deskstop]
        $this->signatureParam = array(
            'api_key' => $this->data['api_key'],
            'api_secret' => $this->data['api_secret'],
            'txn_id' => $this->data['txn_id'],
            'amount' => $this->data['amount'],
            'currency_code' => $this->data['currency_code'],
            'action' => $this->data['action'],
            'timestamp' => $this->data['timestamp']
        );
    }

    function getData() {
        return $this->data;
    }

    function getSignatureParam() {
        return $this->signatureParam;
    }

    function getUrl() {
        return $this->url;
    }

    function getLoadingImgUrl() {
        return $this->loadingImageUrl;
    }

    function getRenderUrl() {
        return $this->renderUrl;
    }

    function getVersion() {
        $this->version = $this->productMetadata->getVersion();
        return $this->version;
    }

    //order status 
    //pipwave status and magento status
    const PIPWAVE_PENDING = \Magento\module-sales\Model\Order::STATE_PENDING_PAYMENT;
    const PIPWAVE_FAIL = \Magento\module-sales\Model\Order::STATE_CANCELED;
    const PIPWAVE_CANCELED = \Magento\module-sales\Model\Order::STATE_CANCELED;
    const PIPWAVE_PAID = \Magento\module-sales\Model\Order::STATE_PROCESSING;
    const PIPWAVE_FULL_REFUNDED = \Magento\module-sales\Model\Order::STATE_CLOSED;
    const PIPWAVE_PARTIAL_REFUNDED = \Magento\module-sales\Model\Order::STATE_PROCESSING;
    const PIPWAVE_SIGNATURE_MISMATCH = \Magento\module-sales\Model\Order::STATE_PENDING_PAYMENT;
    const PIPWAVE_UNKNOWN_STATUS = \Magento\module-sales\Model\Order::STATE_PENDING_PAYMENT;

    //called in module-pipwave\CustomPayment\Controller\Notification\Index
    function processNotification($transaction_status, $order, $refund_amount,$txn_sub_status)
    {
        switch ($transaction_status) {
                case 5: // pending
                    //i didnt test this
                    $status = SELF::PIPWAVE_PENDING;
                    $order->setState($status)->setStatus($status);
                    $order->addStatusHistoryComment('Payment status: Pending payment.')->setIsCustomerNotified(true);
                    break;
                case 1: // failed
                    $status = SELF::PIPWAVE_FAIL;
                    $order->setState($status)->setStatus($status);
                    $order->cancel();
                    $order->addStatusHistoryComment('Payment status: Failed.')->setIsCustomerNotified(true);
                    break;
                case 2: // cancelled
                    $status = SELF::PIPWAVE_CANCELED;
                    $order->setState($status)->setStatus($status);
                    $order->addStatusHistoryComment('Payment status: Canceled.')->setIsCustomerNotified(true);
                    //$status = $method->status_cancelled;
                    break;
                case 10: // complete
                    //$status = SELF::PIPWAVE_PAID;
                    //$order->setState($status)->setStatus($status);
                    
                    //502
                    if ($txn_sub_status==502) {
                        $order->addStatusHistoryComment('Payment status: Paid.')->setIsCustomerNotified(true);

                        //if auto-invoice enabled
                        if ($this->adminConfig->isInvoiceEnabled() == 1) {
                            //create invoice
                            $invoice = $this->invoice->createInvoice($order);

                            $order->addStatusHistoryComment('Invoice created automatically', false);
                            $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice['id']))->setIsCustomerNotified(true);
                            if($invoice && $this->adminConfig->isShippingEnabled( )== 1) {
                                //create shipment
                                $this->shipment->createShipment($order,$invoice);
                                $order->addStatusHistoryComment('Shipment created automatically', false);
                            }
                        }
                    }
                    break;
                case 20: // refunded
                    $status = SELF::PIPWAVE_FULL_REFUNDED;
                    $order->setState($status)->setStatus($status);
                    
                    $invoices = $order->getInvoiceCollection();
                    foreach($invoices as $invoice){
                        $invoiceincrementid = $invoice->getIncrementId();
                    }
                    //var_dump($order);

                    $invoiceObj =  $this->invoice->loadByIncrementId($invoiceincrementid);
                    $creditMemo = $this->creditmemoFactory->createByOrder($order);

                    $creditMemo->setInvoice($invoiceObj);
                    $this->CreditmemoService->refund($creditMemo);
                    
                    $order->addStatusHistoryComment('Payment status: Full refunded.')->setIsCustomerNotified(true);
                    break;
                case 25: // partial refunded
                    $status = SELF::PIPWAVE_PARTIAL_REFUNDED;
                    $order->setState($status)->setStatus($status);
                    
                    $order->addStatusHistoryComment('Payment status: Partial Refunded. Amount: '.$refund_amount)->setIsCustomerNotified(true);
                    break;
                case -1: // signature mismatch
                    $status = SELF::PIPWAVE_SIGNATURE_MISMATCH;
                    $order->setState($status)->setStatus($status);
                    $order->addStatusHistoryComment('Payment status: Signature Mismatch.')->setIsCustomerNotified(true);
                    break;
                default:
                    $status = SELF::PIPWAVE_UNKNOWN_STATUS;
                    $order->setState($status)->setStatus($status);
            }
        return $order;
    }
}