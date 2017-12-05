<?php
namespace module-pipwave\CustomPayment\Model;

class Status
{
    function processNotification($transaction_status, $order, $refund_amount)
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