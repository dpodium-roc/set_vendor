<?php
namespace module-pipwave\CustomPayment\Observer;

use \Magento\framework\Event\Observer;
use \Magento\framework\Event\ObserverInterface;
use \Magento\framework\Controller\ResultFactory;

use \module-pipwave\CustomPayment\Block\InformationNeeded as Information;

/**
 * Class TestObserver
 */
class SalesOrderViewObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        
        if(($block->getNameInLayout() == 'order_info') && ($child = $block->getChild('salesOrderCustomBlock'))){
            $transport = $observer->getTransport();
            if($transport){
                $html = $transport->getHtml();
                $html .= $child->toHtml();
                $transport->setHtml($html);
            }
        }
    }
}