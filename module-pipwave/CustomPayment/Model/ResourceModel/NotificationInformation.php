<?php
namespace module-pipwave\CustomPayment\Model\ResourceModel;

class NotificationInformation extends \Magento\framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('pipwave_order_information', 'order_id');
    }
    protected $_isPkAutoIncrement = false;
}