<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries extends
Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct(){
$this->_controller = 'adminhtml_registries';
$this->_blockGroup = 'kartparadigm_storecredit';
$this->_headerText = Mage::helper('kartparadigm_storecredit')->__('Manage Store Credit Transactions');
parent::__construct();
$this->_removeButton('add');
}
}

