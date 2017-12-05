<?php
namespace module-pipwave\CustomPayment\Model;

use \module-pipwave\CustomPayment\Block\InformationNeeded as Information;

class AdditionalConfigProvider implements \Magento\module-checkout\Model\ConfigProviderInterface
{
    protected $information;
    
    public function __construct(
            Information $information
        ) {
            $this->information = $information;
        }

    function getConfig()
    {
        
        //this is going to be called in view\frontend\web\js\view\payment\method-renderer\custompayment.js
        $loadingimg = $this->information->get_loading_img_url();
        
        $sdkurl = '//staging-checkout.pipwave.com/sdk/';
        
        //get apidata later [this need modify]
        $apidata = json_encode(
            [
                'api_key' => '7oB7eyKpFg60Wn9jAJ6kp407jJqxyk3R9gEVaCTg',
                'token' => '683yoJ9XShzrD7oYGSWgF_iDo5Uz3tqm'
            ]);
        
        $response = 200;
        $url = $this->information->getCompleteUrl();
        
        $config =
        [
            'payment' => 
            [
                'pipwave' =>
                [
                    //this need changes if possible.
                    //set $image_url = *something*
                    //then use magento framework to get url
                    //set $image_url into 'pipwaveImageSrc'
                    'pipwaveImageSrc' => 'https://www.pipwave.com/wp-content/themes/zerif-lite-child/images/logo_bnw.png',
                    'payform' =>
                    [
                        'loadingimg' => $loadingimg,
                        'sdkurl' => $sdkurl,
                        'apidata' => $apidata
                    ],
                    'request' =>
                    [
                        'isResponseGood' => $response
                    ],
                    'controller' =>
                    [
                        'url' => $url
                    ]
                ]
            ]
        ];
        
        return $config;
    }
}