<?php
class Kartparadigm_StoreCredit_Model_Mysql4_Creditinfo_Collection extends
Mage_Core_Model_Mysql4_Collection_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_storecredit/creditinfo');
parent::_construct();
}
}
