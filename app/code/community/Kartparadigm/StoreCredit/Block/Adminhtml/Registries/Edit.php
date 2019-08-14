<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries_Edit
extends Mage_Adminhtml_Block_Widget_Form_Container
{
public function __construct(){
$data = array(
        'label' =>  'Back',
        'onclick'   => 'setLocation(\'' . $this->getUrl('*/credits/customer') . '\')',
        'class'     =>  'back'
   );
$this->addButton ('my_back', $data, 0, 100,  'header'); 
parent::__construct();
$this->_objectId = 'id';
$this->_blockGroup = 'kartparadigm_storecredit';
$this->_controller = 'adminhtml_registries';
$this->_mode = 'edit';
$this->_removeButton('reset');
$this->_updateButton('save', 'label', Mage::helper('kartparadigm_storecredit')->__('Send Credits'));
$this->_removeButton('back');
}

public function getHeaderText(){
if(Mage::registry('registries_data') &&
Mage::registry('registries_data')->getId())
return Mage::helper('kartparadigm_storecredit')->__("Send Credits '%s'", $this->htmlEscape(Mage::registry('registries_data')->getTitle()));
return Mage::helper('kartparadigm_storecredit')->__('Send Credits');
}
}
