<?php
class Kartparadigm_StoreCredit_Adminhtml_CreditsController extends Mage_Adminhtml_Controller_Action
{

public function indexAction()
{
$this->loadLayout();
$this->renderLayout();
return $this;
}
public function editsAction()
{
$this->loadLayout();
$this->renderLayout();
return $this;
}
/* ---------------------------------------------for retriving the customers------------------------------------------   */
public function customerAction()
{
$this->loadLayout();
     $this->renderLayout();
return $this;
}
/*  ------------------------------------------ link to system->configurations of store credit ------------------------------------*/
public function settingsAction()
{
  //  $redirect = '<meta http-equiv="refresh" content="0;url='..'"/> ';
$this->loadLayout();
$url = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/mycustom_section");
        $this->getResponse()->setRedirect($url);
$this->renderLayout();
return $this;
}

public function massDeleteAction()
{
$registryIds = $this->getRequest()->getParam('registries');
if(!is_array($registryIds)) {
Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kartparadigm_storecredit')->__('Please select one or more registries.'));
} else {
try {
$registry = Mage::getModel('kartparadigm_storecredit/creditinfo');
foreach ($registryIds as $registryId) {
$registry->load($registryId)->delete();
}
Mage::getSingleton('adminhtml/session')->addSuccess(
Mage::helper('adminhtml')->__('Total of %d
record(s) were deleted.', count($registryIds))
);
} catch (Exception $e) {
Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
}
}
$this->_redirect('*/*/index');
}
/*-----------------------------------Admin use the store credits in reorder ----------------------------------------------*/
public function usecreditsAction()
{
$array2 = Mage::helper('kartparadigm_storecredit')->getCreditRates();
$balance;
$points;
$discountAmount;
$id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$id)->addFieldToFilter('website1','Main Website')->getLastItem();
$amt = $collection->getTotalCredits();
//Mage::log($amt);
$creditData['totalCredits'] = $amt;
$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
$grandTotal = $quote->getGrandTotal();
$subTotal = $quote->getSubtotal();
Mage::log($quote->getSubtotal()."Sub Total");

if (!Mage::helper('kartparadigm_storecredit')->getTaxEnabled()){
$tax = $quote->getShippingAddress()->getData('tax_amount');
}
 if(!Mage::helper('kartparadigm_storecredit')->getIsShippingEnabled()){
$shippingPrice = $quote->getShippingAddress()->getShippingAmount();
}

$amt1 = ($array2['basevalue'] * $amt) / $array2['credits'];
     $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
    if ($baseCurrencyCode != $currentCurrencyCode) {
       $amt2 = Mage::helper('directory')->currencyConvert($amt1, $baseCurrencyCode, $currentCurrencyCode);
    }
     else{
            $amt2 = $amt1; 
         }

if($grandTotal > $amt2) {


if(($grandTotal - $tax - $shippingPrice) > $amt2){
$discountAmount = $amt2;
$points = round(($discountAmount * $amt)/$amt2);
$balance = 0;
}else{
  $discountAmount = $subTotal;
$points = round(($discountAmount * $amt)/$amt2);
 $balance = $amt - $points;
}
}
else {
$discountAmount = $grandTotal - $tax - $shippingPrice;
$points = round(($discountAmount * $amt)/$amt2);
$balance = $amt - $points;
}
$creditData['discountCredits'] = $points;
//$creditData['balance'] = $balance;

if($discountAmount > 0) {

if ($baseCurrencyCode != $currentCurrencyCode) {
  
$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));

$baseDiscount = $discountAmount/$rates[$currentCurrencyCode];

    }
else{
     $baseDiscount = $discountAmount;
 }
    
            
              //we calculate the Ratio of taxes between GrandTotal & Discount Amount to know how tach we need to remove.
        
               $msg = "Remaining Credits In Your Accoount Are ".$balance;
//Mage::getSingleton('core/session')->setCredits($msg);
              
               
               $quote->setGrandTotal($quote->getGrandTotal()-$discountAmount)
               ->setBaseGrandTotal($quote->getBaseGrandTotal()-$baseDiscount)
               ->setSubtotalWithDiscount($quote->getSubtotal()-$discountAmount)
               ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$baseDiscount)
               ->save();
                     
            $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
            foreach ($quote->getAllAddresses() as $address) {
                
                $address->setSubtotal(0);
                $address->setBaseSubtotal(0);
    
                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
    
                $address->collectTotals();
                
                if($address->getAddressType()==$canAddItems) {
    
                    $address->setSubtotal((float) $quote->getSubtotal());
                    $address->setBaseSubtotal((float) $quote->getBaseSubtotal());
                    $address->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount());
                    $address->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount());
                    $address->setGrandTotal((float) $quote->getGrandTotal());
                    $address->setBaseGrandTotal((float) $quote->getBaseGrandTotal());
                    $address->setDiscountAmount(-$discountAmount);
                    $address->setDiscountDescription('Store Credits');
                    $address->setBaseDiscountAmount(-$baseDiscount);
                    $address->save();
                }//end: if
            } //end: foreach
                foreach($quote->getAllItems() as $item) {

               // We apply discount amount based on the ratio between the GrandTotal and the RowTotal
               $rat = $item->getPriceInclTax() /  $quote->getGrandTotal();
                $rat1 = $item->getBasePriceInclTax() / $quote->getBaseGrandTotal();
               $ratdisc = $discountAmount * $rat;
               $ratdisc1 = $baseDiscount * $rat1;
Mage::log($item->getDiscountAmount()."include tax".$item->getBaseDiscountAmount());
               $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
               $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc1) * $item->getQty())->save();
            }//end: foreach
 Mage::getSingleton('checkout/session')->setCredits($creditData); 
Mage::getSingleton('core/session')->setBalance($balance);
Mage::getSingleton('adminhtml/session')->setValue("true");            
            } 

 
}
/*-------------------------------- end------------------------------------------------------------------------------------- */

/*-----------------------------------Admin unselect the store credits in reorder ----------------------------------------------*/
public function unselectAction()
{
$array2 = Mage::helper('kartparadigm_storecredit')->getCreditRates();
 $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
$total = $quote->getBaseGrandTotal();
$amt1 = array();
$amt1 = Mage::getSingleton('checkout/session')->getCredits(); 
$amt = $amt1['discountCredits'];
$discountAmount;
$amt1 = ($array2['basevalue'] * $amt) / $array2['credits'];
     $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
    if ($baseCurrencyCode != $currentCurrencyCode) {
       $discountAmount = Mage::helper('directory')->currencyConvert($amt1, $baseCurrencyCode, $currentCurrencyCode);
    }
     else{
            $discountAmount = $amt1; 
         }
Mage::log( $discountAmount."discount Amount");

if($discountAmount >= 0) {
            
              //we calculate the Ratio of taxes between GrandTotal & Discount Amount to know how tach we need to remove.
        
               //$msg = "Remaining Credits In Your Accoount Are ".$balance;
//Mage::getSingleton('core/session')->setCredits($msg);
              
               
               $quote->setGrandTotal($quote->getGrandTotal() + $discountAmount)
               ->setBaseGrandTotal($quote->getBaseGrandTotal() + $discountAmount)
               ->setSubtotalWithDiscount($quote->getSubtotal() + $discountAmount)
               ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() + $discountAmount)
               ->save();
                        
            $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
            foreach ($quote->getAllAddresses() as $address) {
                
                $address->setSubtotal(0);
                $address->setBaseSubtotal(0);
    
                $address->setGrandTotal(0);
                $address->setBaseGrandTotal(0);
    
                $address->collectTotals();
                
                if($address->getAddressType()==$canAddItems) {
    
                    $address->setSubtotal((float) $quote->getSubtotal());
                    $address->setBaseSubtotal((float) $quote->getBaseSubtotal());
                    $address->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount());
                    $address->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount());
                    $address->setGrandTotal((float) $quote->getGrandTotal());
                    $address->setBaseGrandTotal((float) $quote->getBaseGrandTotal());
                    $address->save();
                }//end: if
            } //end: foreach
                foreach($quote->getAllItems() as $item){
                 //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                 $rat=$item->getPriceInclTax()/$total;
                 $ratdisc=$discountAmount*$rat;
                 
                 
                 $item->setDiscountAmount($ratdisc);
                 $item->setBaseDiscountAmount($ratdisc)->save();
                
               
               } //end: foreach
Mage::getSingleton('checkout/session')->unsCredits();
Mage::getSingleton('core/session')->unsBalance();
Mage::getSingleton('adminhtml/session')->unsValue();
            
            } 


}
/*-------------------------------- end------------------------------------------------------------------------------------- */
public function showTransactionAction()
{
   $table = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('t_id',$this->getRequest()->getParam('id'));
foreach($table as $t)
{
Mage::getSingleton('adminhtml/session')->setFormData($t);
//Mage::log($t->getActionCredits()."hi");
}
$this->loadLayout();
$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
$this->renderLayout();
}
public function gridAction()
{
$this->getResponse()->setBody(
    $this->getLayout()->createBlock('kartparadigm_storecredit/adminhtml_registries1_grid')->toHtml());
}

}
