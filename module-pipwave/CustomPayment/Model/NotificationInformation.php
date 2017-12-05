<?php
namespace module-pipwave\CustomPayment\Model;

class NotificationInformation extends \Magento\framework\Model\AbstractModel
{
    public function _construct()
    {
        //parent::_construct();
        $this->_init('module-pipwave\CustomPayment\Model\ResourceModel\NotificationInformation');
    }
}