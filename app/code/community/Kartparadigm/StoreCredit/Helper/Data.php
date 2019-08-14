<?php
class Kartparadigm_StoreCredit_Helper_Data extends
Mage_Core_Helper_Abstract {
public function getIsEnabled(){
    return Mage::getStoreConfigFlag('mycustom_section/mycustom_group2/field1'); 
}
public function getTaxEnabled(){
    return Mage::getStoreConfigFlag('mycustom_section/mycustom_group/field1'); 
}
public function getIsShippingEnabled(){
    return Mage::getStoreConfigFlag('mycustom_section/mycustom_group/field2'); 
}
public function getRefundDeductConfig(){
    return Mage::getStoreConfigFlag('mycustom_section/mycustom_group/field3'); //replace section & group with appropriate values.
}
public function getCreditRates(){
$arr = array();
    $arr['credits'] = Mage::getStoreConfig('mycustom_section/mycustom_group4/field1');
    $arr['basevalue'] = Mage::getStoreConfig('mycustom_section/mycustom_group4/field2'); 
      return $arr;
}
public function getCredits($product)
  {
   if(Mage::getStoreConfigFlag('mycustom_section/mycustom_group2/field1')){
    $t=time();
$wid = Mage::app()->getWebsite()->getId();
$cgId = Mage::getSingleton('customer/session')->getCustomerGroupId();

$model = Mage::getResourceModel('catalogrule/rule');

$id = $product->getId();
$rules = $model->getRulesFromProduct($t,$wid,$cgId,$id);
$credits = array();
$sort_order = array(); 
$si;
foreach($rules as $rule)
{
  // var_dump($rule);

$credits[$rule['sort_order']] = $rule['store_credit'];

 //echo " Credits ". $rule['store_credit'] ." \t ";
if($rule['action_stop'] == 1)
$si = $rule['sort_order'];
 
}
ksort($credits);
$totalCredits=0;
foreach($credits as $key=>$sid)
{
if($key == $si){
$totalCredits += $sid;;
break;
} 
$totalCredits += $sid;;
}
     return $totalCredits;

  }
}
}
?>
