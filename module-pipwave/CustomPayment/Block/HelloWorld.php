<?php
namespace module-pipwave\CustomPayment\Block;

class HelloWorld extends \Magento\framework\View\Element\Template
{        
    
    
    public function __construct(
        \Magento\module-backend\Block\Template\Context $context,
        array $data = []
    )
    {  
        parent::__construct($context, $data);
    }
    
    public function getHelloWorld()
    {
        return 'Hello World';
    }
    
    protected $objectManager;
    public function initObjectManager()
    {
        $this->objectManager = \Magento\framework\App\ObjectManager::getInstance();
    }
    // to getApiKey, getApiSecret, getTestMode, getProcessingFee
    // admin config data
    public function getAdminData()
    {
        return $this->objectManager->get('module-pipwave\CustomPayment\Helper\Data');
    }    
    
    public function getCustomerData()
    {
        return $this->objectManager->get('\Magento\module-customer\Model\Session');
    }
    
    public function getUrlLink()
    {
        return $this->objectManager->get('module-pipwave\CustomPayment\Model\Url');
    }
    ////////////////////////////////////////////////////////////////
    
    protected $data;
    protected $signature_param;
    protected $url;
    public function do_all()
    {
        $information = $this->objectManager->get('module-pipwave\CustomPayment\Block\InformationNeeded');
        $information->get_manager();
        
        $information->set_data();
        $this->data = $information->get_data();
        
        //var_dump($this->data);
        
        $information->set_signature_param();
        $information->insert_signature();
        
        $this->data = $information->get_data();
        //echo '<br>data with signature';
        var_dump($this->data);
        
        $this->url = $information->get_url();
        //echo '<br>var dump url';
        var_dump($this->url);
        
        //echo 'i used the sendRequest() here and get this';
        $information->sendRequest();
        
        //from sendRequest()
        $temp = $information->get_response();
        var_dump($temp);
        
        $information->render();
        $form = $information->get_result();
        var_dump($form);
        echo $form;
        
        
    }
    
    public function send_request()
    {
        $information = $this->objectManager->get('module-pipwave\CustomPayment\Model\PipwaveIntegration');
        $information->get_manager();
        
        $agent = $ful->get_agent();
        
        return $ful->send_request_to_pw($this->data, $this->data['api_key'], $this->url, $agent);
    }
}