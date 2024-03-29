<?php
require_once 'Mage/Adminhtml/controllers/CustomerController.php';
class Kartparadigm_StoreCredit_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
 public function saveAction()
    {
        $data = $this->getRequest()->getPost();
//Mage::log($data['credit_value']);

if ($data) {

//Inset the credits added by admin

$date = new Zend_Date(Mage::getModel('core/date')->timestamp());
	$date=$date->toString('Y-M-d H:m:s');
$col = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$data['customer_id'])->addFieldToFilter('website1',$data['website1'])->getLastItem();
$tdata = array();
$tdata['c_id'] =  $data['customer_id'];
$tdata['action_credits'] = $data['credit_value'];


// Action Of The Admin
if($col->getTotalCredits() == '')
$tdata['action'] = "Created";
else 
$tdata['action'] = "Updated";
//
$tdata['website1'] = $data['website1'];
if($data['notification'] == 'on')
$tdata['customer_notification_status'] = "Notified";
else 
 $tdata['customer_notification_status'] = "No";
$tdata['state'] = "completed";
$tdata['custom_msg'] = "By admin : admin (".$data['description'].")";
$tdata['action_date'] = $date;
$tdata['store_view'] = $data['storeview1'];

$tdata['total_credits'] = $col->getTotalCredits() + ($data['credit_value']);
if($tdata['total_credits'] <= 0)
{
$tdata['action_credits'] = -$col->getTotalCredits();  
$tdata['total_credits'] = 0;
  
}

Mage::log($col->getTotalCredits());

if(isset($data['credit_value']) && ($data['credit_value'] != 0) &&  ($col->getTotalCredits() > 0 || ($col->getTotalCredits() == '' && $data['credit_value'] > 0) || $tdata['total_credits'] > 0))
{


$model = Mage::getModel('kartparadigm_storecredit/creditinfo')->setData($tdata);
try{

     $model->save();
if($data['notification'] === 'on'){

$templateName = Mage::getStoreConfig('mycustom_section/mycustom_group1/caritor_email_template');

//Mage::log($templateName."sreehari");
// for sending mail to admin after order placed
$emailTemplate = Mage::getModel('core/email_template')
            ->loadDefault('caritor_email_template');
$name = Mage::getStoreConfig('mycustom_section/mycustom_group1/sender_email_identity');
$group = "ident_".$name;

$email = Mage::getStoreConfig('trans_email/'.$group.'/email');


    $emailTemplateVariables = array();
    $emailTemplateVariables['var1'] = $data['account']['firstname'];
    $emailTemplateVariables['var2'] = $tdata['action_credits'];
    $emailTemplateVariables['var3'] = $data['storeview1'];
    $emailTemplateVariables['var4'] = $tdata['total_credits'];
    $emailTemplateVariables['var5'] = $tdata['action'];
    $emailTemplateVariables['var6'] = $tdata['action_date'];
    $emailTemplateVariables['var7'] = $data['description'];
     $emailTemplateVariables['var8'] = $this->getUrl('customer/account/login/');

   $emailTemplate->getProcessedTemplate($emailTemplateVariables);
Mage::log($name);

   $emailTemplate->setSenderName($name);
   $emailTemplate->setSenderEmail($email);
   $emailTemplate->setReplyTo($email);
    try {
   $emailTemplate->send($data['account']['email'], $data['account']['firstname'], $emailTemplateVariables);
    } catch (Exception $e) {
        echo $e->getMessage();
    } 
              
//
}

  }catch(Exception $e)
   {

 echo $e->getMessage();
   }
}
  

//

            $redirectBack = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(false)
            ;

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

            $errors = $customerForm->validateData($formData);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    $isDefaultBilling = isset($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $index;
                    $address->setIsDefaultBilling($isDefaultBilling);
                    $isDefaultShipping = isset($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $index;
                    $address->setIsDefaultShipping($isDefaultShipping);

                    $errors = $addressForm->validateData($formData);
                    if ($errors !== true) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array(
                            'id' => $customer->getId())
                        ));
                        return;
                    }

                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
            }

            // Default billing and shipping
            if (isset($data['account']['default_billing'])) {
                $customer->setData('default_billing', $data['account']['default_billing']);
            }
            if (isset($data['account']['default_shipping'])) {
                $customer->setData('default_shipping', $data['account']['default_shipping']);
            }
            if (isset($data['account']['confirmation'])) {
                $customer->setData('confirmation', $data['account']['confirmation']);
            }

            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')
                && !$customer->getConfirmation()
            ) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = false;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(true);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = true;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                $customer->save();

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );
                Mage::dispatchEvent('adminhtml_customer_save_after', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $customer->getId(),
                        '_current' => true
                    ));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
    }
/*------------------------------Send credits Form to multiple customers------------------------------------ */
    
public function sendCreditsAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
$registry = Mage::getModel('customer/customer');

if ($customersIds) {
$emails = array();
$i = 0;
foreach($customersIds as $id2)
{
 $registry->load((int) $id2);
$emails[] = array(
 'label' => $registry->getEmail(),
'value' => $id2 
);
}
//Mage::register('registry_data', $emails);
Mage::getSingleton('adminhtml/session')->setFormData($emails);
$this->loadLayout();
$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
$this->renderLayout();
}
 
   }
/*------------------------------------------sending credits to multiple customers-------------------------------- */

public function sendAction()
    {
        Mage::log(count($this->getRequest()->getParams()));
     $count =  count($this->getRequest()->getParams());
        $customersIds = $this->getRequest()->getParam('email');
        
           $credits = $this->getRequest()->getParam('credits');

         $message = $this->getRequest()->getParam('message');
         $storeview = $this->getRequest()->getParam('store_view');
   
           if($this->getRequest()->getParam('notify'))
Mage::log("hhhhh");
$currentTimestamp = Mage::getModel('core/date')->timestamp(time()); 
$nowdate = date('Y-m-d H:m:s', $currentTimestamp);
foreach($customersIds as $id){

$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$id)->addFieldToFilter('website1','Main Website')->getLastItem();

$arr1['c_id'] = $id;
     $arr1['website1'] = "Main Website";
     $arr1['action_credits'] = $credits;
     $arr1['total_credits'] = $collection->getTotalCredits() + $credits;
     $arr1['action'] = "Updated";
     $arr1['state'] = 0;
     $arr1['store_view'] = $storeview;
     $arr1['action_date'] = $nowdate;
     $arr1['custom_msg'] = "Send By Admin: admin( ".$message." )";
     $arr1['customer_notification_status'] = 'No';

$table1 = Mage::getModel('kartparadigm_storecredit/creditinfo');
$table1->setData($arr1);

try{
$table1->save();
if($count == 7){
$emailTemplate = Mage::getModel('core/email_template')
            ->loadDefault('caritor_email_template');
$name = Mage::getStoreConfig('mycustom_section/mycustom_group1/sender_email_identity');
$group = "ident_".$name;

$email = Mage::getStoreConfig('trans_email/'.$group.'/email');

$customerData = Mage::getModel('customer/customer')->load($id)->getData();

    $emailTemplateVariables = array();
    $emailTemplateVariables['var1'] = $customerData['firstname'];
    $emailTemplateVariables['var4'] = $arr1['total_credits'];
    $emailTemplateVariables['var5'] = $message;
    $emailTemplateVariables['var8'] = $this->getBaseUrl();

   $emailTemplate->getProcessedTemplate($emailTemplateVariables);
//Mage::log($name);

   $emailTemplate->setSenderName($name);
   $emailTemplate->setSenderEmail($email);
   $emailTemplate->setReplyTo($email);
    try {
   $emailTemplate->send($customerData['email'], $customerData['firstname'], $emailTemplateVariables);
    } catch (Exception $e) {
        echo $e->getMessage();
    } 
      }//end if   for sending credits email update        
}catch(Exception $e){
Mage::log($e);
}

}
$this->_redirect('*/credits/customer');
    }

}
