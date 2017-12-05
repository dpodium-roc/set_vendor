<?php
namespace module-pipwave\CustomPayment\Model;

use \Magento\module-payment\Model\Method\AbstractMethod;

class Url extends AbstractMethod
{
    protected $urlBuilder;
    protected $UrlHelper;
    
    public function __construct(
        \Magento\framework\UrlInterface $urlBuilder,
        \module-pipwave\CustomPayment\Helper\UrlData $UrlHelper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->UrlHelper = $UrlHelper;
    }

    function getUrl($test_mode) {
        if ($test_mode == 'yes' || $test_mode == 1) {
            $url = $this->UrlHelper::URL_TEST;
        } else {
            $url = $this->UrlHelper::URL;
        }
        return $url;
    }

    function getRenderUrl($test_mode) {
        if ($test_mode == 'yes' || $test_mode == 1) {
            $url = $this->UrlHelper::RENDER_URL_TEST;
        } else {
            $url = $this->UrlHelper::RENDER_URL;
        }
        return $url;
    }

    function getLoadingImageUrl($test_mode) {
        if ($test_mode == 'yes' || $test_mode == 1) {
            $url = $this->UrlHelper::LOADING_IMAGE_URL_TEST;
        } else {
            $url = $this->UrlHelper::LOADING_IMAGE_URL;
        }
        return $url;
    }

    function defaultSuccessPageUrl() {
        return $this->urlBuilder->getUrl($this->UrlHelper::SUCCESS_URL);
    }

    function defaultFailPageUrl() {
        $temp = $this->urlBuilder->getUrl($this->UrlHelper::FAIL_URL);
        return $temp;
    }

    function notificationPageUrl() {
        return $this->urlBuilder->getUrl($this->UrlHelper::NOTIFICATION_URL);
    }
}