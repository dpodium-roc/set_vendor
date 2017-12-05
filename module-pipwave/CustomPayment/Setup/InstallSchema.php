<?php
namespace module-pipwave\CustomPayment\Setup;

use \Magento\framework\Setup\InstallSchemaInterface;
use \Magento\framework\Setup\ModuleContextInterface;
use \Magento\framework\Setup\SchemaSetupInterface;
use \Magento\framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        // table - to save information when pipwave send info to notification url
        $tableName = $installer->getTable('pipwave_order_information');
        
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            
            // Create table
            $table = $installer->getConnection()
                //->newTable('pipwave_order_information');
                ->newTable($tableName)
                ->addColumn(
                    'order_id',
                    Table::TYPE_TEXT,
                    20,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ORDER_ID'
                )
                ->addColumn(
                    'pw_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Pw_id'
                )
                ->addColumn(
                    'txn_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Txn_id'
                )
                ->addColumn(
                    'pg_txn_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Pg_txn_id'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Amount'
                )
                ->addColumn(
                    'tax_exempted_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Tax_exempted_amount'
                )
                ->addColumn(
                    'processing_fee_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Processing_fee_amount'
                )
                ->addColumn(
                    'shipping_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Shipping_amount'
                )
                ->addColumn(
                    'handling_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Handling_amount'
                )
                ->addColumn(
                    'tax_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Tax_amount'
                )
                ->addColumn(
                    'total_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Total_amount'
                )->addColumn(
                    'final_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Final_amount'
                )
                ->addColumn(
                    'currency_code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Currency_code'
                )
                ->addColumn(
                    'subscription_token',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Subscription_token'
                )
                ->addColumn(
                    'charge_index',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Charge_index'
                )
                ->addColumn(
                    'payment_method_code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Payment_method_code'
                )
                ->addColumn(
                    'payment_method_title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Payment_method_title'
                )
                ->addColumn(
                    'reversible_payment',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Reversible_payment'
                )
                ->addColumn(
                    'settlement_account',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Settlement_account'
                )
                ->addColumn(
                    'require_capture',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Require_capture'
                )
                ->addColumn(
                    'transaction_status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Transaction_status'
                )
                ->addColumn(
                    'mobile_number',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Mobile_number'
                )
                ->addColumn(
                    'mobile_number_country_code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Mobile_number_country_code'
                )
                ->addColumn(
                    'mobile_number_verification',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Mobile_number_verification'
                )
                ->addColumn(
                    'risk_service_type',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Risk_service_type'
                )
                ->addColumn(
                    'aft_score',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Aft_score'
                )
                ->addColumn(
                    'aft_status',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Aft_status'
                )
                ->addColumn(
                    'pipwave_score"',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Pipwave_score'
                )
                ->addColumn(
                    'rules_action',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Rules_action'
                )
                ->addColumn(
                    'risk_management_data',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Risk_management_data'
                )
                ->addColumn(
                    'matched_rules',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Matched_rules'
                )
                ->addColumn(
                    'txn_sub_status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Txn_sub_status'
                )
                ->setComment('Information sent from pipwave after buyer made payment');
            $installer->getConnection()->createTable($table);
        }
     
        $installer->endSetup();
        
        
    }
}