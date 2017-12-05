define(
    [
        'ko',
        'jquery',
        'Magento_module-checkout/js/view/payment/default',
        'Magento_module-checkout/js/action/place-order',
        'Magento_module-checkout/js/action/select-payment-method',
        'Magento_module-checkout/js/model/quote',
        'Magento_module-customer/js/model/customer',
        'Magento_module-checkout/js/model/payment-service',
        'Magento_module-checkout/js/checkout-data',
        'Magento_module-checkout/js/model/checkout-data-resolver',
        'uiRegistry',
        'Magento_module-checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messages',
        'uiLayout',
        'Magento_module-checkout/js/action/redirect-on-success',
        'mage/url'
        
    ],
    function (
        ko,
        $,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        quote,
        customer,
        paymentService,
        checkoutData,
        checkoutDataResolver,
        registry,
        additionalValidators,
        Messages,
        layout,
        redirectOnSuccessAction,
        urlBuilder
    ) {
        'use strict';
        
        return Component.extend(
        {
            defaults: {
                template: 'module-pipwave_CustomPayment/payment/custompayment'
            },
            
            //pipwave logo url
            getPipwaveImageSrc: function () {
                return window.checkoutConfig.payment.pipwave.pipwaveImageSrc;              
            },
            
            //start overwriting
            
            redirectAfterPlaceOrder: false,
            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),
            
            /**
             * Place order.
             */
            placeOOrder: function (data, event) {
                var self = this;
                //self.afterPlaceOrder();
                
                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                            function () {
                                
                                //self.goodResult=false;
                                self.afterPlaceOrder();
                                $(".actions-toolbar").hide();
                            }
                        );
                    return true;
                    //hide 'placeOrder' button
                    //$("#placeOrderButton").hide();
                }
                
                return false;
				
            },
            
            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                // Override this function and put after place order logic here
                
                
                var ControllerUrl = urlBuilder.build("index");
                $.ajax({
                    type: "GET",
                    url: ControllerUrl,
                    success: function (result) {
                        
                        if (result['status']==200){
                            $("#pipform").html("<div id='pwscript' class='text-center'></div><div id='pwloading' style='text-align: center;'><img src='"+result['loadingImageUrl']+"' /></div><script>var pwconfig = "+result['apiData']+";(function (_, p, w, s, d, k) {var a = _.createElement('script');a.setAttribute('src', w + d);a.setAttribute('id', k);setTimeout(function() {var reqPwInit = (typeof reqPipwave != 'undefined');if (reqPwInit) {reqPipwave.require(['pw'], function(pw) {pw.setOpt(pwconfig);pw.startLoad();});} else {_.getElementById(k).parentNode.replaceChild(a, _.getElementById(k));}}, 800);})(document, 'script', '"+result['sdkUrl']+"', 'pw.sdk.min.js', 'pw.sdk.min.js', 'pwscript');</script>");
                        } else {
                            $("#pipform").html('status: '+result['status']+'<br>message: '+result['message']+'<br> Please try another payment method.');
                        }  
                    },
                });
                //$("#placeOrderButton").hide();
            },
            
            /**
             * @return {*}
             */
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                );
            },
            
            /**
             * @return {Boolean}
             */
            selectPaymentMethod: function () {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },        
        });
    }
);
