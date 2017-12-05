<?php
namespace module-pipwave\CustomPayment\Model;

class Config
{
    //fire api
    const URL = 'https://api.pipwave.com/payment';
    const URL_TEST = 'https://staging-api.pipwave.com/payment';
    
    //render sdk
    const RENDER_URL = 'https://secure.pipwave.com/sdk/';
    const RENDER_URL_TEST = 'https://staging-checkout.pipwave.com/sdk/';
    
    //loading image
    const LOADING_IMAGE_URL = 'https://secure.pipwave.com/images/loading.gif';
    const LOADING_IMAGE_URL_TEST = 'https://staging-checkout.pipwave.com/images/loading.gif';
    
    //magento controller url
    const SUCCESS_URL = 'checkout/onepage/success';
    const FAIL_URL = 'checkout/onepage/failure';
    const NOTIFICATION_URL = 'notification/notification/index';
    
    
    
    
}