<?php
class Kartparadigm_StoreCredit_Model_Creditinfo extends
Mage_Core_Model_Abstract
{
public function __construct()
{
$this->_init('kartparadigm_storecredit/creditinfo');
parent::_construct();
}

 public function getWebsites() {
$site = Mage::getResourceModel('core/website_collection');
$websites = array();
$i = 0;
 foreach($site as $s)
     $websites[ $i++ ] = $s['name'];
     
        return $websites;

    }
public function getStoreviews() {
$_storeName = array();
$i=0;
$site = Mage::app()->getWebsites();
foreach($site as $s){
$allStores = $s->getStoreIds();
foreach ($allStores as $_eachStoreId => $val) 
  {
    $_storeName[Mage::app()->getStore($_eachStoreId)->getName()] = Mage::app()->getStore($_eachStoreId)->getName();
  }

}
return $_storeName;
}

/*-------------------------------------------Return the credits Of the product to the observer-------------------------------------*/

public function getCreditsOfProduct($pid)
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
