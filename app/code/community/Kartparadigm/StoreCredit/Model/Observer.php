<?php
class Kartparadigm_StoreCredit_Model_Observer
{
    /**
     * Exports an order after it is placed
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return boolean
     */
    


  public function set_credits_discountamount($observer)
  {
$array2 = Mage::helper('kartparadigm_storecredit')->getCreditRates();
//Mage::log(Mage::helper('kartparadigm_storecredit')->getRefundDeductConfig() . " configuration settings ");
//Mage::log($array2);
 $session = Mage::getSingleton('checkout/session');
 if(Mage::helper('customer')->isLoggedIn()) {
  $customer = Mage::getSingleton('customer/session')->getCustomer();
  $customer_group=Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomerGroupId())->getCustomerGroupCode();
}
 
$val1 = Mage::getSingleton('adminhtml/session')->getValue();
if(isset($val1))
{
$amt1 = array();
$amt1 = Mage::getSingleton('checkout/session')->getCredits();
$amt = $amt1['totalCredits'];
$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
}
else{
$amt1 = array();
 $quote = Mage::getModel('checkout/cart')->getQuote();
$amt1 = Mage::getSingleton('checkout/session')->getCredits();
$amt = $amt1['discountCredits'];
}
  $isvirtual=0;
    foreach($quote->getAllItems() as $item){
     if($item->getIsVirtual()==1) {
     $isvirtual=1;
     }
     if(Mage::getModel('catalog/product')->load($item->getProductId())->getTypeId()=='customproduct'){
     $isvirtual=1;
     }
    }
$total=$quote->getGrandTotal(); 

$subTotal = $quote->getSubtotal();
//Mage::log($quote->getGrandTotal()."this is grand total store credit");
//Mage::log($quote->getSubtotal()."this is sub total");

 if (!Mage::helper('kartparadigm_storecredit')->getTaxEnabled()){
$tax = $quote->getShippingAddress()->getData('tax_amount');
}
 if(!Mage::helper('kartparadigm_storecredit')->getIsShippingEnabled()){
$shippingPrice = $quote->getShippingAddress()->getShippingAmount();
}
$totalCredits1 = array();
$totalCredits1 = Mage::getSingleton('checkout/session')->getCredits();
$totalCredits = $totalCredits1['totalCredits'];

$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp); 
//echo $expirydate;
//echo $nowdate;
$balance;
//echo $amt;
$amt2;
 $amt1;
       

if(isset($amt)){
/*---------------------Calculating Default Currency Value----------------------- */
$amt1 = ($array2['basevalue'] * $amt) / $array2['credits'];
     $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
    if ($baseCurrencyCode != $currentCurrencyCode) {
       $amt2 = Mage::helper('directory')->currencyConvert($amt1, $baseCurrencyCode, $currentCurrencyCode);
    }
     else{
            $amt2 = $amt1; 
         }
      // $amt2 = Mage::helper('core')->currency($amt1, true, false);
//Mage::log($amt1." = amount = ".$amt2 );
//----------------------------------------------------------------
if($total > $amt2) {
if(($total - $tax - $shippingPrice) > $amt2){
$discountAmount = $amt2;
$balance = $totalCredits - $amt;
}else{
  $discountAmount = $subTotal;
$points = round(($discountAmount * $amt)/$amt2);
//Mage::log($points."Conver Points");
$balance = $totalCredits - $points;
}
}
else {
$discountAmount = $total - $tax - $shippingPrice;
$points = round(($discountAmount * $amt)/$amt2);
//Mage::log($points."Conver Points");
$balance = $totalCredits - $points;
}
Mage::getSingleton('core/session')->setBalance($balance);

Mage::getSingleton('checkout/session')->setDiscount($totalCredits - $balance);
$msg = "Current Credits In Your Account : " . $balance;
Mage::getSingleton('core/session')->setCredits($msg);
   if ($discountAmount > 0) {


if ($baseCurrencyCode != $currentCurrencyCode) {
  
$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));

$baseDiscount = $discountAmount/$rates[$currentCurrencyCode];

    }
else{
     $baseDiscount = $discountAmount;
 }       
            $total = $quote->getBaseSubtotal();
             $data1 = $quote->getData();
            // Mage::log($data1['entity_id']."quote id");
            $quote->setSubtotal(0);
            $quote->setBaseSubtotal(0);
            $quote->setSubtotalWithDiscount(0);
            $quote->setBaseSubtotalWithDiscount(0);
            $quote->setGrandTotal(0);
            $quote->setBaseGrandTotal(0);
            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
            foreach($quote->getAllAddresses() as $address) {
               $data = $address->getData();
               $address->setSubtotal(0);
               $address->setBaseSubtotal(0);
               $address->setGrandTotal(0);
               $address->setBaseGrandTotal(0);
               $address->collectTotals();
               $quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
               $quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());
               $quote->setSubtotalWithDiscount((float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount());
               $quote->setBaseSubtotalWithDiscount((float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount());
               $quote->setGrandTotal((float)$quote->getGrandTotal() + $address->getGrandTotal());
               $quote->setBaseGrandTotal((float)$quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
               $quote->setEntityId($data1['entity_id'])->save();
               $quote->setSubtotalWithDiscount($quote->getSubtotal() - $discountAmount)->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $baseDiscount)->setEntityId($data1['entity_id'])->save();
               if ($address->getAddressType() == $canAddItems) {
                  $address->setSubtotalWithDiscount((float)$data['subtotal_with_discount'] - $discountAmount);
                  $address->setGrandTotal((float)$data['grand_total'] - $discountAmount);
                  $address->setBaseSubtotalWithDiscount((float)$data['base_subtotal_with_discount'] - $baseDiscount);
                  $address->setBaseGrandTotal((float)$data['base_grand_total'] - $baseDiscount);
                  if ($data['discount_description']) {
                     $address->setDiscountAmount(($data['discount_amount'] - $discountAmount));
                     $address->setDiscountDescription($data['discount_description'] . ', Store Credits');
                     $address->setBaseDiscountAmount(($data['base_discount_amount'] - $baseDiscount));
                  }
                  else {
                     $address->setDiscountAmount(-($discountAmount));
                     $address->setDiscountDescription('Store Credits');
                     $address->setBaseDiscountAmount(-($baseDiscount));
                  }
                  $address->setAddressId($data['address_id'])->save();
               }
            }
            foreach($quote->getAllItems() as $item) {

               // We apply discount amount based on the ratio between the GrandTotal and the RowTotal
               $rat = $item->getPriceInclTax() /  $quote->getSubtotal();
                $rat1 = $item->getBasePriceInclTax() / $quote->getBaseSubtotal();
               $ratdisc = $discountAmount * $rat;
               $ratdisc1 = $baseDiscount * $rat1;
//Mage::log($item->getDiscountAmount()."include tax".$item->getBaseDiscountAmount());
              // Mage::log($item->getDiscountAmount()."discount storecredit");
$idata = $item->getData();
              Mage::log($item->getDiscountAmount()."discount amount credit");
               $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
               $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc1) * $item->getQty())->save();
            }
         }else if($totalCredits == 0){

$msg = "Sorry You Have No Credits In Your Account";
Mage::getSingleton('core/session')->setCredits($msg);
}
  
 
 } 
 }

/*-----------------------------------------------------After checkout Inserting data into table ---------------------------   */

public function debitOrder(Varien_Event_Observer $observer)
{ 

  try {
       $arr = array();    
$id = Mage::getModel("sales/order")->getCollection()->getLastItem()->getIncrementId();

$order = Mage::getModel('sales/order')->loadByIncrementId($id);
$_grand = $order->getGrandTotal();
$custname = $order->getCustomerName();
//echo "<br> cumstomer id :".$order->getCustomerId();
$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp); 
$amt =Mage::getSingleton('checkout/session')->getDiscount();
$totalCredits1 = array();
$totalCredits1 = Mage::getSingleton('checkout/session')->getCredits();
$totalCredits = $totalCredits1['totalCredits'];
$balance = Mage::getSingleton('core/session')->getBalance();
$arr['c_id'] = $order->getCustomerId();
$arr['action_credits'] = - $amt;
$arr['total_credits'] = $balance;
$arr['store_view'] = Mage::app()->getStore()->getName();
$arr['state'] = 1;
$arr['order_id'] = $id;
$arr['action'] = "Used";
$arr['custom_msg'] = "By User:Using For Order " . $arr['order_id'];
$arr['customer_notification_status'] = 'Notified';
$arr['action_date'] = $nowdate;
$arr['website1'] = "Main Website";
 
            if($amt > 0) {
              $credits = Mage::getModel('kartparadigm_storecredit/creditinfo');

                $model = Mage::getModel('kartparadigm_storecredit/creditinfo')->setData($arr);

try{

     $model->save();
}
catch(Exception $e)
   {

 echo $e->getMessage();
   }

                $successMessage =  Mage::helper('kartparadigm_storecredit')->__('Credits Inserted Successfully');
                Mage::getSingleton('core/session')->addSuccess($successMessage);

           
          
            }else{
                //throw new Exception("Insufficient Data provided");
            }

        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirectUrl($this->_getRefererUrl());
        }  

Mage::getSingleton('checkout/session')->unsCredits();
          Mage::getSingleton('core/session')->unsBalance();
          Mage::getSingleton('core/session')->unsCredits();
Mage::getSingleton('adminhtml/session')->unsValue();
Mage::getSingleton('checkout/session')->unsDiscount();
    
}
/* --------------------------------------------------- Refund The Credits To Customer Account --------------------------------- */

public function addCredits($observer)
{ 

$creditmemo = $observer->getCreditmemo();
//Mage::log($creditmemo);
//Mage::log($creditmemo->getBaseGrandTotal());
    $order = $creditmemo->getOrder();
//Mage::log($order);
$store_id = $order->getStoreId();
$website_id = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
$website = Mage::app()->getWebsite($website_id); 
//Mage::log( $website->getName());
$sName =  Mage::app()->getStore($store_id)->getName();
//Mage::log( $sid);
//Mage::log(Mage::getSingleton('adminhtml/session')->getTotal()['status']);


if (Mage::helper('kartparadigm_storecredit')->getRefundDeductConfig())
{
  // Deduct the credits which are gained at the time of invoice

   $credits = array(); 
   $currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
   $nowdate = date('Y-m-d H:m:s', $currentTimestamp);
   $credits['c_id'] = $order->getCustomerId();
   $credits['order_id'] = $order->getIncrementId();
   $credits['website1'] = 'Main Website';
   $credits['store_view'] = $sName;
   $credits['action_date'] = $nowdate;
   $credits['action'] = "Deducted";
   $credits['customer_notification_status'] = 'Notified';
   $credits['state'] = 1; 
   //$credits['custom_msg'] = 'By admin : Deducted the Credits of the Order ' . $credits['order_id'] ;

   foreach ($creditmemo->getAllItems() as $item) {
     $orderItem = Mage::getResourceModel('sales/order_item_collection');       
     $orderItem->addIdFilter($item->getOrderItemId()); 
     $data = $orderItem->getData();

     //Mage::log($data);
     $credits['action_credits'] = - ($data[0]['credits'] * $item->getQty());

     $collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$order->getCustomerId())->addFieldToFilter('website1','Main Website')->getLastItem();

     $totalcredits = $collection->getTotalCredits();
     $credits['total_credits'] = $totalcredits + $credits['action_credits'] ;
     $credits['custom_msg'] = "By User:For Return The Product " .$item->getName()." For Quantity " . round($item->getQty()) ; //Custom Message
    $credits['item_id'] = $item->getOrderItemId();
    $table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
    $table1->setData($credits);
    try{
     if($credits['action_credits'] != 0)
      $table1->save();
     }catch(Exception $e){
        Mage::log($e);
      }
   }

// End Deduct the credits which are gained at the time of invoice

}
//end
$status = array();
$status = Mage::getSingleton('adminhtml/session')->getTotal(); 
if($status['status'] == 1)
            {  

 $val = array();    
 
 
                     $val['c_id'] = $order->getCustomerId();
                    
                     $val['order_id'] = $order->getIncrementId();
                     
                      $val['website1'] = $website->getName();
                     
                      $val['store_view'] = $sName;
                     

$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$val['c_id'])->addFieldToFilter('website1',$val['website1'])->getLastItem();
                        $currentCurrencyRefund1 = array();
                        $currentCurrencyRefund1 = Mage::getSingleton('adminhtml/session')->getTotal();
                     $currentCurrencyRefund = $currentCurrencyRefund1['credits'];
/*------------------------Convert Current currency(refunded amount is current currency) to credit points-------------- */
$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
$baseCurrency;
if ($baseCurrencyCode != $currentCurrencyCode) {
  
$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));

$baseCurrency = $currentCurrencyRefund/$rates[$currentCurrencyCode];

    }
else{
    $baseCurrency = $currentCurrencyRefund;
 }
$array2 = Mage::helper('kartparadigm_storecredit')->getCreditRates();
//$amt1 = ($array2['basevalue'] * $amt) / $array2['credits'];
if(isset($array2)){
$refundCredits =  round(($array2['credits'] * $baseCurrency) / $array2['basevalue']); 
}
else{
$refundCredits = round($baseCurrency);
}
/*---------------------end------------------ */
                      $val['action_credits'] = $refundCredits;
                     $val['total_credits'] =  $collection->getTotalCredits() + $refundCredits;
                     $val['action_date'] = $nowdate; 
                     $val['action'] = "Refunded";
                     $val['custom_msg'] = 'By admin : return product by customer to the order ' . $val['order_id'] ;
                     $val['customer_notification_status'] = 'Notified'; 
                     $val['state'] = 0;
//Mage::getSingleton('adminhtml/session')->unsTotal();
$model = Mage::getSingleton('kartparadigm_storecredit/creditinfo');
//Mage::log($creditmemo->getDiscountAmount());
//Mage::log($creditmemo->getDiscountDescription());
//checking 
if($creditmemo->getDiscountDescription() == "Store Credits"){
$total = $creditmemo->getGrandTotal() - ($creditmemo->getDiscountAmount());
}
else{
$total = $creditmemo->getGrandTotal();
}
$model->setData($val);
try{
if($total >=  $currentCurrencyRefund){
if( $currentCurrencyRefund > 0)
{

$model->save();

}
}
else{

Mage::getSingleton('adminhtml/session')->setErr('true');

}

} catch(Mage_Core_Exception $e){
//Mage::log($e);
}

}
}

/*-----------------------------------------------------Unset Existing Sessions---------------------------   */

public function unset_session_inreorder(Varien_Event_Observer $observer)
{ 

$value = Mage::getSingleton('checkout/session')->getCredits();
if(isset($value)){
Mage::getSingleton('checkout/session')->unsCredits();
          Mage::getSingleton('core/session')->unsBalance();
          Mage::getSingleton('core/session')->unsCredits();
Mage::getSingleton('adminhtml/session')->unsValue();
Mage::getSingleton('checkout/session')->unsDiscount();

}
}
/*----------------------------------------------- end ------------------------------------  */
/*--------------------------------------------------- Event At The Time Of Login ------------------------------------------------*/
public function assigncredits(Varien_Event_Observer $observer)
{
   $customer = $observer->getCustomer();
$arr = array();
$arr1 = array();
   //Mage::log($customer->getEmail());
   //Mage::log($customer->getId()."customer id");
$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp); //current data
$collection = Mage::getModel('kartparadigm_storecredit/sendcreditstofriend')->getCollection()->addFieldToFilter('receiver_email',$customer->getEmail())->addFieldToFilter('status',0); //retriving values from sendcreditstofriend
//Mage::log(count($collection));
$arr = array();
if(count($collection) > 0){
foreach($collection as $col){
//Mage::log($col['s_id']."sender id");
if($col['status'] == 0){

$arr1['c_id'] = $customer->getId();
     $arr1['website1'] = "Main Website";
     $arr1['action_credits'] = $col['credits'];
     $arr1['total_credits'] = $col['credits'];
     $arr1['action'] = "Updated";
     $arr1['state'] = 0;
     $arr1['store_view'] = Mage::app()->getStore()->getName();
     $arr1['action_date'] = $nowdate;
      $arr1['custom_msg'] = "Send By : " . $col['sname']." Message( ".$col['message']." )";
     $arr1['customer_notification_status'] = 'No';

$arr['status'] = 1;

$id = $col['receiver_id'];

     $table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table1->setData($arr1);

$table2 = Mage::getModel('kartparadigm_storecredit/sendcreditstofriend')->load($id)->addData($arr);

/*-------------------------------------------------------------------------------------*/
try{

$table1->save();
$table2->setId($id)->save();
//$this->_redirect('*/*/');

}catch(Exception $e){
Mage::log($e);
}



}//end if
}//end foreach 
}//end if
}



/*-----------------------------------Save Store Credit in Cart ---------------------------------------------*/
public function catalogProductLoadAfter(Varien_Event_Observer $observer)
{
    // set the additional options on the product
 if(Mage::getStoreConfigFlag('mycustom_section/mycustom_group2/field1')){
    $action = Mage::app()->getFrontController()->getAction();
    if ($action->getFullActionName() == 'checkout_cart_add')
    {

          
        $product = $observer->getProduct();
       //  Mage::log($product);
            
$totalCredits = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCreditsOfProduct($product->getId());

        if($totalCredits > 0) {

       // if ($options = $action->getRequest()->getParam('extra_option'))
        //{
            //$product = $observer->getProduct();
           // Mage::log($product);

            // add to the additional options array
            $additionalOptions = array();
            if ($additionalOption = $product->getCustomOption('additional_options'))
            {
                $additionalOptions = (array) unserialize($additionalOption->getValue());
            }

           
                $additionalOptions[] = array(
                    'label' => "Credits",
                    'value' => $totalCredits,
                );

           
            // add the additional options array with the option code additional_options
            $observer->getProduct()
                ->addCustomOption('additional_options', serialize($additionalOptions));
        //}
          }
    }
 }
}
/*-----------------------------------Save Store Credit in the Order ---------------------------------------------*/
public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
{
$quoteItem = $observer->getItem();
    if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
        $orderItem = $observer->getOrderItem();
        $options = $orderItem->getProductOptions();
        $options['additional_options'] = unserialize($additionalOptions->getValue());
$var;
if (count($options['additional_options'] > 0)) {
                  if ($options['additional_options'][0]['value'] != '') 
                     $i = 0;
                    $val = $options['additional_options'][0]['value'];

}
//Mage::log($val." trgy");
$orderItem->setCredits($val);
        $orderItem->setProductOptions($options);
    }

}
/*--------------------------------Add Credit to the Customer Storecredit --------------------------------------   */


public function getCustomOptionsOfProduct(Varien_Event_Observer $observer)
{
    $invoice = $observer->getEvent()->getInvoice();
$invoidData = $invoice->getData();
//Mage::log($invoidData);
$orderId = $invoidData['order_id'];
$cid = $invoidData['customer_id'];
$order1 = Mage::getModel('sales/order')->load($orderId);
    $Incrementid = $order1->getIncrementId();
//Mage::log( $Incrementid."Order Id"); 

// Assign Field To Inset Into CreditInfo Table

$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp);   // Current TimeStamp

$arr['c_id'] = $cid;//Customer Id
$arr['store_view'] = Mage::app()->getStore()->getName(); //Storeview
$arr['state'] = 1; //State Of Transaction
$arr['order_id'] = $Incrementid;  //OrderId
$arr['action'] = "Crdits";  //Action
$arr['customer_notification_status'] = 'Notified'; //Email Sending Status
$arr['action_date'] = $nowdate;   //Current TimeStamp
$arr['website1'] = "Main Website"; //Website

foreach ($invoice->getAllItems() as $item) {

//Mage::log($item->getQty()."Item Quntity");

$orderItem = Mage::getResourceModel('sales/order_item_collection');       
 $orderItem->addIdFilter($item->getOrderItemId()); 
$data = $orderItem->getData();
$arr['action_credits'] = $data[0]['credits'] * $item->getQty();

$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$cid)->addFieldToFilter('website1','Main Website')->getLastItem();

$totalcredits = $collection->getTotalCredits();
$arr['total_credits'] = $arr['action_credits'] + $totalcredits;
$arr['custom_msg'] = "By User:For Purchage The Product " .$item->getName()." For Quantity " . round($item->getQty()) ; //Custom Message
$arr['item_id'] = $item->getOrderItemId();
$table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table1->setData($arr);
try{
    if($arr['action_credits'] > 0)
      $table1->save();

}catch(Exception $e){
    Mage::log($e);
   }

}//end Foreach



}
/* -------------------------------AddCancelOrderCreditToCustomerAccount----------------------------------------------   */
public function addCancelOrderCredits(Varien_Event_Observer $observer)
{
$arr = array();
$item = $observer->getEvent()->getItem();
Mage::log($item['base_discount_amount']);
$qty = $item['qty_ordered'] - $item['qty_invoiced'];
$orderId = $item['order_id'];
$order1 = Mage::getModel('sales/order')->load($orderId);
Mage::log($Incrementid . "Increament Id");
if($order1['discount_description'] == "Store Credits")
{ 
  Mage::log("hiiiiii");
  $arr['order_id'] = $order1->getIncrementId();
if($qty > 0){
//-----------------------

//$rat = $item['base_price_incl_tax'] /  $order1['base_subtotal'];
   // Mage::log($item['base_discount_amount']."");            
               //Mage::log("QUANTITY "  . $qty);
               $baseDiscount = ($item['base_discount_amount'] * $qty) / $item['qty_ordered'];
  //calculating Points
$array2 = Mage::helper('kartparadigm_storecredit')->getCreditRates();
 
    $points = ($array2['credits'] * $baseDiscount) / $array2['basevalue']  ;


$arr['action_credits'] = $points;
Mage::log($arr['action_credits']."return credits of the product");

//-----------------------------------------------
}  
$arr['c_id'] = $order1['customer_id'];
  $currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
  $arr['action_date'] = date('Y-m-d H:m:s', $currentTimestamp);
  $arr['store_view'] = Mage::app()->getStore()->getName(); //Storeview
  $arr['state'] = 1; //State Of Transaction
  $arr['website1'] = "Main Website"																; //Website
  $arr['action'] = "Refunded";
  $arr['custom_msg'] = "By Admin:For Cancel The Order " . $arr['order_id']." Of Product With Name " .$item['name']; //Custom Message
  $collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$arr['c_id'])->addFieldToFilter('website1','Main Website')->getLastItem();

$totalcredits = $collection->getTotalCredits();
$arr['total_credits'] = $arr['action_credits'] + $totalcredits;
$arr['customer_notification_status'] = 'Notified'; //Email Sending Status
$table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table1->setData($arr);
try{
    if($arr['action_credits'] > 0)
      $table1->save();

}catch(Exception $e){
    Mage::log($e);
   }
} 
} 
/*------------------Disable Module------------------------------------------*/
public function disableModule(Varien_Event_Observer $observer)
{
 
 $conf = $observer->getEvent();
$moduleName = 'Kartparadigm_StoreCredit';
 $nodePath = "/modules/$moduleName/active";
  //Mage::log($conf);
  Mage::log($conf['modules'][65]);
 Mage::log("hai");
   if(!Mage::helper('core/data')->isModuleOutputEnabled($moduleName))
    {
              Mage::getConfig()->setNode($nodePath, 'false', true);
              $outputPath = "advanced/modules_disable_output/$moduleName";
               if (!Mage::getStoreConfig($outputPath)) {
                     Mage::app()->getStore()->setConfig($outputPath, true);
                 }
$resource = Mage::getSingleton('core/resource');
 $writeConnection = $resource->getConnection('core_write');

         
    $query = 'UPDATE core_config_data SET value = 0 WHERE path="mycustom_section/mycustom_group2/field1"';
     $writeConnection->query($query);
       // Mage::log('kkkkkkkkkkkkkkk');

     }else{
          $resource = Mage::getSingleton('core/resource');
 $writeConnection = $resource->getConnection('core_write');

         
    $query = 'UPDATE core_config_data SET value = 1 WHERE path="mycustom_section/mycustom_group2/field1"';
     $writeConnection->query($query);
             }
      
    
   
}



}
