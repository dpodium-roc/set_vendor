<?php
namespace module-pipwave\CustomPayment\Controller\Index;

class Index extends \Magento\framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $information;
    protected $pipwaveIntegration;
    protected $order;

    public function __construct(
        \Magento\framework\App\Action\Context $context,
        \Magento\framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\module-sales\Model\Order $order,
        \module-pipwave\CustomPayment\Block\InformationNeeded $information,
        \module-pipwave\CustomPayment\Model\PipwaveIntegration $pipwaveIntegration
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->order = $order;
        $this->information = $information;
        $this->pipwaveIntegration = $pipwaveIntegration;
    }

    public function execute()
    {
        //run function
        $this->information->prepareData();

        //variables
        $data = $this->information->getData();

        //get signature param, put into data
        $signatureParam = $this->information->getSignatureParam();
        $data['signature'] = $this->pipwaveIntegration->generatePwSignature($signatureParam);

        $url = $this->information->getUrl();
        $agent = $this->pipwaveIntegration->getAgent();

        $response = $this->pipwaveIntegration->sendRequestToPw($data, $data['api_key'], $url, $agent);

        $renderUrl = $this->information->getRenderUrl();
        $loadingImageUrl = $this->information->getLoadingImgUrl();
        $callerVersion = $this->information->getVersion();

        //return to module-pipwave\CustomPayment\view\frontend\web\js\method-renderer\custompayment.js
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            if ($response['status'] == 200) {
                $test = [
                    'loadingImageUrl' => $loadingImageUrl,
                    'apiData'=> json_encode([
                                    'api_key' => $data['api_key'],
                                    'token' => $response['token'],
                                    'caller_version' => $callerVersion
                                ]),
                    'sdkUrl' => $renderUrl,
                    'status' => $response['status']
                ];
            } else {
                $test = [
                    'data' => $data,
                    'status' => $response['status'],
                    'message' => $response['message']
                ];
            }
            $result->setData($test);
            return ($result);
        }
    }
}