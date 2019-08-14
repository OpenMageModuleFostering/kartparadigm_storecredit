<?php
class Kartparadigm_StoreCredit_Block_Credit extends Mage_Core_Block_Template
{


public function displayCreditsOfProduct($pid)
{
//Mage::log($pid." is product id");
$t=time();
$wid = Mage::app()->getWebsite()->getId();
$cgId = Mage::getSingleton('customer/session')->getCustomerGroupId();

$model = Mage::getResourceModel('catalogrule/rule');

$id = $pid;
$rules = $model->getRulesFromProduct($t,$wid,$cgId,$id);
$credits = array();
$sort_order = array(); 
$i;
$totalCredits = 0;
foreach($rules as $rule)
{
  // var_dump($rule);

$credits[$rule['sort_order']] = $rule['store_credit'];

 //echo " Credits ". $rule['store_credit'] ." \t ";
if($rule['action_stop'] == 1)
$i = $rule['sort_order'];
 
}
 ksort($credits);
 
 foreach($credits as $key=>$sid)
  {
   if($key == $i){
     $totalCredits += $sid;;
     break;
    } 
   $totalCredits += $sid;;
  }
    //Mage::log("total Credits of the product " . $totalCredits);
    return $totalCredits;
 } 
}
