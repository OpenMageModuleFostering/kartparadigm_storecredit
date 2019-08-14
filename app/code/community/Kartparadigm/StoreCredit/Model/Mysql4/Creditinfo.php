<?php
class Kartparadigm_StoreCredit_Model_Mysql4_Creditinfo extends
Mage_Core_Model_Mysql4_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_storecredit/creditinfo', 't_id');
}
}
