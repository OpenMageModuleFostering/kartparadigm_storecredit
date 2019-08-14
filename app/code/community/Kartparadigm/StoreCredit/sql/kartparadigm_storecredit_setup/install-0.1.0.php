<?php

$installer = $this;
$installer->startSetup();

/**
 * Create Registry Gift Table 
 *
 *
 */

$tableName = $installer->getTable('kartparadigm_storecredit/creditinfo');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('kartparadigm_storecredit/creditinfo'))
        ->addColumn('t_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Transaction Id')
       ->addColumn('c_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 25, array(
            'nullable'  => true,
        ), 'Customer Id')
       ->addColumn('website1', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true,
        ), 'Website')
       ->addColumn('total_credits', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Total Credits')
      ->addColumn('action_credits', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => true,
        ), 'Credit Due To Action')
        ->addColumn('action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true,
        ), 'Operaration On Credits')
       ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
                'nullable'  => true,
            ), 'State Of Credits')
        ->addColumn('store_view', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
                'nullable'  => true,
            ), 'Store View')
        ->addColumn('action_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
            array(),
            'Created Date')
        ->addColumn('custom_msg', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable'  => true,
        ), 'Custom Message')
        ->addColumn('customer_notification_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable'  => true,
        ), 'Customer Notification Status')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 100, array(
            'nullable'  => true,
        ), 'Order For Credits')
         ->addForeignKey(
            $installer->getFkName(
                'kartparadigm_storecredit/creditinfo',
                'c_id',
                'customer/entity',
                'entity_id'
            ),
      'c_id', $installer->getTable('customer/entity'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);         
     $installer->getConnection()->createTable($table);
}
$installer->endSetup();
