<?php
namespace module-pipwave\CustomPayment\Model\Ui;

use \Magento\module-checkout\Model\ConfigProviderInterface;



final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'pipwave_custompayment';
    
    public function getConfig()
    {
        return
        [
            'payment' =>
            [
                self::CODE =>
                [
                    'transactionResults' =>
                    [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ]
                ]
            ]
        ];
    }
}