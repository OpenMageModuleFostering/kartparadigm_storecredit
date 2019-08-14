<?php
class Kartparadigm_StoreCredit_Model_Mysql4_Sendcreditstofriend extends
Mage_Core_Model_Mysql4_Abstract
{
public function _construct()
{
$this->_init('kartparadigm_storecredit/sendcreditstofriend', 'receiver_id');
}
}
