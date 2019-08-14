<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Customer_Edit_Tab_Credits
extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {
public function __construct()
{
$this->setTemplate('kartparadigm/storecredit/customer/main.phtml');
parent::_construct();
}
public function getCustomerId()
{
return Mage::registry('current_customer')->getId();
}
public function getTabLabel()
{
return $this->__('Store Credit');
}
public function getTabTitle()
{
return $this->__('Store Credit');
}
public function canShowTab()
{
return true;
}
public function isHidden()
{
return false;
}
}

