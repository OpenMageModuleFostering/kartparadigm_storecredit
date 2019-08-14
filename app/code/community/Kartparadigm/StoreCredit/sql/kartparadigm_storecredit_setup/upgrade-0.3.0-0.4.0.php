<?php

$installer = $this;
$installer->startSetup();

/**
 * Create Sending Credits Tables Of Store Credit
 *
 *
 */

$tableName = $installer->getTable('kartparadigm_storecredit/sendcreditstofriend');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('kartparadigm_storecredit/sendcreditstofriend'))
        ->addColumn('receiver_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Receiver Id')
       ->addColumn('s_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 25, array(
            'nullable'  => true,
        ), 'Sender Id')
       ->addColumn('sname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable'  => true,
        ), 'Sender Name')
       ->addColumn('receiver_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true,
        ), 'Receiver Name')
       ->addColumn('receiver_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable'  => true,
        ), 'Receiver Email')
      ->addColumn('credits', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Sending Credits')
        ->addColumn('message', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true,
        ), 'Message To Receiver') 
         ->addColumn('date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
            array(),
            'Send Date')
         ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Status Of Shared Credits')
         ->addForeignKey(
            $installer->getFkName(
                'kartparadigm_storecredit/sendcreditstofriend',
                's_id',
                'customer/entity',
                'entity_id'
            ),
      's_id', $installer->getTable('customer/entity'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);         
     $installer->getConnection()->createTable($table);
}
$installer->endSetup();
