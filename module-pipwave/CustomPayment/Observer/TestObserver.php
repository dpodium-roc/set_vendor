<?php
namespace module-pipwave\CustomPayment\Observer;

use \Magento\framework\Event\Observer;
use \Magento\framework\Event\ObserverInterface;
use \Magento\framework\Controller\ResultFactory;

use \module-pipwave\CustomPayment\Block\InformationNeeded as Information;

/**
 * Class TestObserver
 */
class TestObserver implements ObserverInterface
{

    protected $_responseFactory;
    protected $_url;
    
    protected $iinformation;
    
     public function __construct(
        \Magento\framework\App\ResponseFactory $responseFactory,
        \Magento\framework\UrlInterface $url,
        Information $information
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->iinformation = $information;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /*
        $event = $observer->getEvent();
        $CustomRedirectionUrl = $this->_url->getUrl('pipwave/CustomPayment/Controller/Index/Index');
        $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
        return $this;
        
        */
        $information = $this->iinformation;
        $information->get_manager();
        
        $information->set_data();
        $this->data = $information->get_data();
        
        //var_dump($this->data);
        
        $information->set_signature_param();
        $information->insert_signature();
        
        $this->data = $information->get_data();
        //echo '<br>data with signature';
        //var_dump($this->data);
        
        $this->url = $information->get_url();
        //echo '<br>var dump url';
        //var_dump($this->url);
        
        //echo 'i used the sendRequest() here and get this';
        $information->sendRequest();
        
        //from sendRequest()
        $temp = $information->get_response();
        
    }
}