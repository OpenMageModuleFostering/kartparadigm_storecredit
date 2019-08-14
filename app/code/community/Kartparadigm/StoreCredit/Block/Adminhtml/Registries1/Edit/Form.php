<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries1_Edit_Form
extends Mage_Adminhtml_Block_Widget_Form
{
protected function _prepareForm(){
$form = new Varien_Data_Form(array(
'id' => 'edit_form',
'method' => 'post',
'enctype' => 'multipart/form-data'
));
$form->setUseContainer(true);
$this->setForm($form);
$data;
if (Mage::getSingleton('adminhtml/session')->getFormData()){
$data = Mage::getSingleton('adminhtml/session')->getFormData();
Mage::log($data);
Mage::getSingleton('adminhtml/session')->setFormData(null);
}elseif(Mage::registry('registry_data'))
$data = Mage::registry('registry_data')->getData();
$fieldset = $form->addFieldset('registry_form',
array('legend'=>Mage::helper('kartparadigm_storecredit')->__('Credits information')));

$fieldset->addField('t_id', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Transaction Id'),
'name'=> 'tid',
'disabled' => 'true',
'value' => $data['t_id'],
));

$fieldset->addField('action_credits', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Credits Applaid'),
'name'=> 'credits',
'disabled' => 'true',
'value' => $data['action_credits'],
));
$fieldset->addField('total_credits', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('current credits'),
'name'=> 'tcredits',
'disabled' => 'true',
'value' => $data['total_credits'],
));
$fieldset->addField('c_id', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('customer Id'),
'name'=> 'cid',
'disabled' => 'true',
'value' => $data['c_id'],
));
$fieldset->addField('action', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Action'),
'name'=> 'action',
'disabled' => 'true',
'value' => $data['action'],
));
$fieldset->addField('action_date', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Transaction Date'),
'name'=> 'date',
'disabled' => 'true',
'value' => $data['action_date'],
));
$fieldset->addField('custom_msg', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Message'),
'name'=> 'message',
'disabled' => 'true',
'value' => $data['custom_msg'],
));
$form->setValues($data);
return parent::_prepareForm();
}
}
?>
