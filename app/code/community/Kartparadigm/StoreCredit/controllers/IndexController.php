<?php
class Kartparadigm_StoreCredit_IndexController extends Mage_Core_Controller_Front_Action
{
/* ---------------------- Display The Store Credit Account --------------------------------------------- */
public function indexAction()
{
/* -----------------Checking Customer Is Login Or Not -------------------------------------------------  */
if(Mage::getSingleton('customer/session')->isLoggedIn()) {
   $handles = array('default');
   $handles[] = 'customer_account';// setting the handle for same layout
   $this->loadLayout($handles);
   $this->renderLayout();
}
else 
$this->_redirect('customer/account/login');
}
/* ------------------------------ Apply Credit TO The Total -------------------------------------------------------- */
public function applyCreditsAction()
{
if(Mage::getSingleton('customer/session')->isLoggedIn()) {
     $customerData = Mage::getSingleton('customer/session')->getCustomer();
 $id = $customerData->getId();
$creditData = array();
$data = $this->getRequest()->getParam('Credits');
//Mage::log($data);
$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$id)->addFieldToFilter('website1',Mage::app()->getWebsite()->getName())->getLastItem();
$totalcredits = $collection->getTotalCredits();
//Mage::log('totalcredits'.$totalcredits);
//$collection = Mage::getModel('tele_storecredit/creditinfo')->getCollection()->addFieldToFilter('gift_code',$data);
if(count($collection)>=1 && $data <= $totalcredits && $data > 0){
$creditData['totalCredits'] = $totalcredits; 
$creditData['discountCredits'] = $data;
 Mage::getSingleton('checkout/session')->setCredits($creditData);

}

else if($data > $totalcredits) {
Mage::getSingleton('checkout/session')->unsCredits();
$msg = "Please Enter Less Than Or Equal to " . $totalcredits ." Credits ";
Mage::getSingleton('core/session')->setCredits($msg);
}
else {
Mage::getSingleton('checkout/session')->unsCredits();
Mage::getSingleton('core/session')->unsBalance();
$msg = "Please Enter Greater Than 0 Credits ";
Mage::getSingleton('core/session')->setCredits($msg);
}
}
else {
$msg = "Please Login To Applay Credits";
Mage::getSingleton('core/session')->setCredits($msg);
}

$this->_redirect('checkout/cart/');
}
public function cancelCreditsAction()
{

$this->_redirect('checkout/cart/index');
}

public function reAction()
{
   $p1 = Mage::app()->getRequest()->getPost('p1');
$response['status'] = 0;
$sidebar= $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
$response['refreshtotalBLK'] = $sidebar;
return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
 }

public function updateAction()
{

$creditData['totalCredits'] = $_POST['total'];
 $creditData['discountCredits'] = $_POST['discount'];
  Mage::getSingleton('checkout/session')->setCredits($creditData);
Mage::getSingleton('core/session')->unsCredits();

//Mage::log($_POST);


}
/* --------------------------------- sharing credits to friend---------------------------------------------------- */
public function sendcreditsAction()
{

$data = $this->getRequest()->getParams();
Mage::log($data);

$collection1 = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$data['id'])->addFieldToFilter('website1','Main Website')->getLastItem();
if($collection1->getTotalCredits() >= $data['credits']){

/*-------------------- value store in sendcreditstofriend table-------------------------------------*/
$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp); 
$arr = array();
$arr1 = array();
$arr['s_id'] = $data['id'];
$arr['receiver_name'] = $data['name'];
$arr['receiver_email'] = $data['email'];
$arr['credits'] = $data['credits'];
$arr['message'] = $data['message'];
$arr['date'] = $nowdate;
$arr['status'] = 0;
$arr['sname'] = $data['sname'];
/*-----------------------------------------------------------------------------------------*/


//Mage::log($arr['date']);
$users = mage::getModel('customer/customer')->getCollection()
           ->addAttributeToSelect('email');

foreach ($users as $user)
  {

if($user->getData()['email'] == $arr['receiver_email'])
 {

$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$user->getData()['entity_id'])->addFieldToFilter('website1','Main Website')->getLastItem();

/*  ------------------------if the customer exists add credits to his account -------------------------------------- */
     $arr1['c_id'] = $user->getData()['entity_id'];
     $arr1['website1'] = "Main Website";
     $arr1['action_credits'] = $data['credits'];
     $arr1['total_credits'] = $collection->getTotalCredits() + $data['credits'];
     $arr1['action'] = "Updated";
     $arr1['state'] = 0;
     $arr1['store_view'] = Mage::app()->getStore()->getName();
     $arr1['action_date'] = $nowdate;
     $arr1['custom_msg'] = "Send By : " . $data['sname']." Message( ".$data['message']." )";
     $arr1['customer_notification_status'] = 'No';

$arr['status'] = 1;

     $table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table1->setData($arr1);

/*-------------------------------------------------------------------------------------*/
try{

$table1->save();
//$this->_redirect('*/*/');

}catch(Exception $e){
Mage::log($e);
}
    
 }
}
/*-----------------------------------deduct the sender credits from sender store credit account ---------------------------*/
     $arr2['c_id'] = $data['id'];
     $arr2['website1'] = "Main Website";
     $arr2['action_credits'] = -$data['credits'];
$collection1 = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$data['id'])->addFieldToFilter('website1','Main Website')->getLastItem();

     $arr2['total_credits'] = $collection1->getTotalCredits() - $data['credits'];
     $arr2['action'] = "Updated";
     $arr2['state'] = 0;
     $arr2['store_view'] = Mage::app()->getStore()->getName();
     $arr2['action_date'] = $nowdate;
     $arr2['custom_msg'] = "Send To : " . $data['name']." Message( ".$data['message']." )";
     $arr2['customer_notification_status'] = 'No';
     

$table2 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table2->setData($arr2);

$table = Mage::getModel('kartparadigm_storecredit/sendcreditstofriend');
$table->setData($arr);
try{

$table->save();
$table2->save();

$emailTemplate = Mage::getModel('core/email_template')
            ->loadDefault('caritor_email_template1');
$name = Mage::getStoreConfig('mycustom_section/mycustom_group1/sender_email_identity');
$group = "ident_".$name;

$email = Mage::getStoreConfig('trans_email/'.$group.'/email');


    $emailTemplateVariables = array();
    $emailTemplateVariables['var1'] = $data['name'];
    $emailTemplateVariables['var2'] = $data['credits'];
    $emailTemplateVariables['var3'] = $data['sname'];
    $emailTemplateVariables['var4'] = $nowdate;
    $emailTemplateVariables['var5'] = $data['message'];
    $emailTemplateVariables['var8'] = "http://localhost/jaypore/index.php/customer/account/login/";

   $emailTemplate->getProcessedTemplate($emailTemplateVariables);
//Mage::log($name);

   $emailTemplate->setSenderName($name);
   $emailTemplate->setSenderEmail($email);
   $emailTemplate->setReplyTo($email);
    try {
   $emailTemplate->send($data['email'], $data['name'], $emailTemplateVariables);
    } catch (Exception $e) {
        echo $e->getMessage();
    } 
              
}catch(Exception $e){
Mage::log($e);
}

}
else{
$msg = "Please Enter less than " . $collection1->getTotalCredits()." Credits";
Mage::getSingleton("core/session")->setMsg($msg);

}
$this->_redirect('*/*/');
}
}
