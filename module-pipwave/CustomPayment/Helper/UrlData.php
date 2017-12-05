<?php
namespace module-pipwave\CustomPayment\Helper;

//constant url
class UrlData {
    //url to fire api [get  from pipwave]
    const URL = 'https://api.pipwave.com/payment';
    const URL_TEST = 'https://staging-api.pipwave.com/payment';

    //url to render sdk [get from pipwave]
    const RENDER_URL = 'https://secure.pipwave.com/sdk/';
    const RENDER_URL_TEST = 'https://staging-checkout.pipwave.com/sdk/';

    //url for loading image [get from pipwave]
    const LOADING_IMAGE_URL = 'https://secure.pipwave.com/images/loading.gif';
    const LOADING_IMAGE_URL_TEST = 'https://staging-checkout.pipwave.com/images/loading.gif';

    //url for controller [get from magento]
    const SUCCESS_URL = 'checkout/onepage/success';
    const FAIL_URL = 'checkout/onepage/failure';
    const NOTIFICATION_URL = 'notification/notification/index';
}