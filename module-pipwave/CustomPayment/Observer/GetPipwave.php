<?php
namespace module-pipwave\CustomPayment\Observer;

use \Magento\framework\Event\Observer;

class GetPipwave implements \Magento\framework\Event\ObserverInterface
{
    public function execute(Observer $observer)
    {
        $text='hi i am here';
        return $text;
    }
}