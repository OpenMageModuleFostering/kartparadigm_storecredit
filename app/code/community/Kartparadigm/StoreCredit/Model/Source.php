<?php
class Kartparadigm_StoreCredit_Model_Source 
{

public function toOptionArray()
  {
    return array(
      
      array('value' => 0, 'label' => Mage::helper('kartparadigm_storecredit')->__('No')),
     array('value' => 1, 'label' => Mage::helper('kartparadigm_storecredit')->__('Yes')),
     // and so on...
    );
  }

}
