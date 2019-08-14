<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries1 extends
Mage_Adminhtml_Block_Widget_Grid_Container
{
public function __construct(){
$this->_controller = 'adminhtml_registries1';
$this->_blockGroup = 'kartparadigm_storecredit';
$this->_headerText = Mage::helper('kartparadigm_storecredit')->__('Manage Customer Credits');
parent::__construct();
$this->_removeButton('add');
}
}

