<?php
namespace module-pipwave\CustomPayment\Model\ResourceModel\NotificationInformation;
class Collection extends \Magento\framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'module-pipwave\CustomPayment\Model\NotificationInformation',
            'module-pipwave\CustomPayment\Model\ResourceModel\NotificationInformation'
            );
    }
}