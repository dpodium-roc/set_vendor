<?php
namespace module-pipwave\CustomPayment\Block\Adminhtml\Order\View;

/*
this class wil be used by
module-pipwave\CustomPayment\Controller\Notification\Index
module-pipwave\CustomPayment\view\adminhtml\template\order\view\info.phtml
*/
class Info extends \Magento\module-backend\Block\Widget
{
    protected $transaction_status;
    protected $refund;
    protected $txn_sub_status;
    protected $signatureParam;
    protected $signature;
    protected $pipwave_score;
    protected $rule_action;
    protected $message;
    protected $data;
    protected $_coreRegistry;

    public function __construct(
        \Magento\module-backend\Block\Template\Context $context,
        \Magento\framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    //called in module-pipwave\CustomPayment\Controller\Notification\Index
    function setData($post_data) {
        //variables
        $payment_method = 'pipwave' . (!empty($post_data['payment_method_title']) ? (" - " . $post_data['payment_method_title']) : "");

        //variables below used for $signatureParam
        $timestamp = (isset($post_data['timestamp']) && !empty($post_data['timestamp'])) ? $post_data['timestamp'] : time();
        $pw_id = (isset($post_data['pw_id']) && !empty($post_data['pw_id'])) ? $post_data['pw_id'] : '';
        $order_number = (isset($post_data['txn_id']) && !empty($post_data['txn_id'])) ? $post_data['txn_id'] : '';
        $amount = (isset($post_data['amount']) && !empty($post_data['amount'])) ? $post_data['amount'] : '';
        $currency_code = (isset($post_data['currency_code']) && !empty($post_data['currency_code'])) ? $post_data['currency_code'] : '';
        $this->transaction_status = (isset($post_data['transaction_status']) && !empty($post_data['transaction_status'])) ? $post_data['transaction_status'] : '';

        //used to compare with $newSignature
        $this->signature = (isset($post_data['signature']) && !empty($post_data['signature'])) ? $post_data['signature'] : '';

        //used in processNotification($transaction_status, $order, $refund, $txn_sub_status)
        $total_amount = (isset($post_data['total_amount']) && !empty($post_data['total_amount'])) ? $post_data['total_amount'] : 0.00;
        $final_amount = (isset($post_data['final_amount']) && !empty($post_data['final_amount'])) ? $post_data['final_amount'] : 0.00;
        $this->refund = $total_amount - $final_amount;
        $this->txn_sub_status = (isset($post_data['txn_sub_status']) && !empty($post_data['txn_sub_status'])) ? $post_data['txn_sub_status'] : time();

        // pipwave risk execution result
        $this->pipwave_score = isset($post_data['pipwave_score']) ? $post_data['pipwave_score'] : '';
        $this->rule_action = isset($post_data['rules_action']) ? $post_data['rules_action'] : '';
        $this->message = isset($post_data['message']) ? $post_data['message'] : '';
        //$shipping = (isset($post_data['shipping_info']) && !empty($post_data['shipping_info'])) ? $post_data['shipping_info'] : '';
        //used in generate_pw_signature($signatureParam)
        $this->signatureParam = array(
            'timestamp' => $timestamp,
            'pw_id' => $pw_id,
            'txn_id' => $order_number,
            'amount' => $amount,
            'currency_code' => $currency_code,
            'transaction_status' => $transaction_status,
        );

        $this->data =[
            'order_id' => $order_number, 
            'pw_id' => $pw_id, 
            'txn_id' => $post_data['txn_id'], 
            'pg_txn_id' => $post_data['pg_txn_id'], 
            'amount' => $post_data['amount'], 
            'tax_exempted_amount' => $post_data['tax_exempted_amount'], 
            'processing_fee_amount' => $post_data['processing_fee_amount'], 
            'shipping_amount' => $post_data['shipping_amount'], 
            'handling_amount' => $post_data['handling_amount'], 
            'tax_amount' => $post_data['tax_amount'], 
            'total_amount' => $post_data['total_amount'], 
            'final_amount' => $post_data['final_amount'], 
            'currency_code' => $post_data['currency_code'], 
            'subscription_token' => $post_data['subscription_token'], 
            'charge_index' => $post_data['charge_index'], 
            'payment_method_code' => $post_data['payment_method_code'], 
            'payment_method_title' => $post_data['payment_method_title'], 
            'reversible_payment' => $post_data['reversible_payment'], 
            'settlement_account' => $post_data['settlement_account'], 
            'require_capture' => $post_data['require_capture'], 
            'transaction_status' => $post_data['transaction_status'], 
            'mobile_number' => $post_data['mobile_number'], 
            'mobile_number_country_code' => $post_data['mobile_number_country_code'], 
            'mobile_number_verification' => $post_data['mobile_number_verification'], 
            'risk_service_type' => $post_data['risk_service_type'], 
            'aft_score' => $post_data['aft_score'], 
            'aft_status' => $post_data['aft_status'], 
            'pipwave_score' => $post_data['pipwave_score'], 
            'rules_action' => $post_data['rules_action'], 
            'risk_management_data' => json_encode($post_data['risk_management_data']), 
            'matched_rules' => json_encode($post_data['matched_rules']), 
            'txn_sub_status' => $post_data['txn_sub_status'] 
        ];
    }

    //called in module-pipwave\CustomPayment\Controller\Notification\Index
    function get_signatureParam() {
        return $this->signatureParam;
    }
    function getRefund() {
        return $this->refund;
    }
    function getTransactionStatus() {
        return $this->transaction_status;
    }
    function getTransactionSubStatus() {
        return $this->txn_sub_status;
    }
    function getData() {
        return $this->data;
    }
    function getSignature() {
        return $this->signature;
    }
    function getPipwaveScore() {
        return $this->pipwave_score;
    }
    function getRuleAction() {
        return $this->rule_action;
    }
    function getMessage() {
        return $this->message;
    }
    
    
    //called in module-pipwave\CustomPayment\Controller\Notification\Index
    function compareSignature($signature, $newSignature) {
        if ($signature != $newSignature) {
            $this->transaction_status = -1;
        }
    }
    
    //called in module-pipwave\CustomPayment\view\adminhtml\template\order\view\info.phtml
    function getOrder() {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new \Magento\framework\Exception\LocalizedException(__('We can\'t get the order instance right now.'));
    }

    //called in module-pipwave\CustomPayment\view\adminhtml\template\order\view\info.phtml
    function showInfo($id) {
        $model = $this->NotificationInformationFactory->create();
        //$text = $model->load($id);
        //$text = $this->NotificationInformationFactoryDB->create()->load($model, $id);
        //->addFieldToFilter('order_id',$id)
        $model->load($id);
        $text = $model;
        //$text = $text['order_id'];
        return $text;
    }
}