<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries_Edit_Form
extends Mage_Adminhtml_Block_Widget_Form
{
protected function _prepareForm(){
$form = new Varien_Data_Form(array(
'id' => 'edit_form',
'action' => $this->getUrl('*/*/send', array('id'=> $this->getRequest()->getParam('id'))),
'method' => 'post',
'enctype' => 'multipart/form-data'
));
$form->setUseContainer(true);
$this->setForm($form);
$data;
if (Mage::getSingleton('adminhtml/session')->getFormData()){
$data = Mage::getSingleton('adminhtml/session')->getFormData();
Mage::getSingleton('adminhtml/session')->setFormData(null);
}elseif(Mage::registry('registry_data'))
$data = Mage::registry('registry_data')->getData();
$fieldset = $form->addFieldset('registry_form',
array('legend'=>Mage::helper('kartparadigm_storecredit')->__('Credits information')));

$fieldset->addField('email', 'multiselect', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('email Id'),
'name'=> 'email',
'class'=> 'required-entry',
'required' => true,
'values' => $data,
));

$fieldset->addField('credits', 'text', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Credits'),
'class'=> 'required-entry',
'required' => true,
'name'=> 'credits',
));

$fieldset->addField('notify', 'checkbox', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Notify Customers'),
'value' => 'true',
'name'=> 'notify',
));

$fieldset->addField('store_view', 'select', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Store View'),
'name'=> 'store_view',
'options'=> Mage::getModel('kartparadigm_storecredit/creditinfo')->getStoreviews(),
));

$fieldset->addField('message', 'textarea', array(
'label' => Mage::helper('kartparadigm_storecredit')->__('Message'),
'class'=> 'required-entry',
'required' => true,
'name'=> 'message',
));


$form->setValues($data);
return parent::_prepareForm();
}
}
?>
