<?php
 
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->getConnection()
    ->addColumn($installer->getTable('kartparadigm_storecredit_creditinfo'),
    'itemId',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => null,
        'comment' => 'Item Ids'
    )
);
 
$installer->endSetup();
