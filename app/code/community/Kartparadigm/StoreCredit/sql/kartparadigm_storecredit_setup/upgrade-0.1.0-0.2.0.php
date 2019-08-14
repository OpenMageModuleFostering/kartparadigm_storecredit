<?php
 
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order'),
    'refundedstorecredit_amount',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => null,
        'comment' => 'Refundedstorecredit Amount'
    )
);
 
$installer->endSetup();
