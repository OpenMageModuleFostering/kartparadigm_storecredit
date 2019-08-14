<?php
class Kartparadigm_StoreCredit_Model_Resource_Transaction extends Mage_Core_Model_Resource_Transaction

{


    /**
     * Initialize objects save transaction
     *
     * @return Mage_Core_Model_Resource_Transaction
     * @throws Exception
     */
    public function save()
    {
        $this->_startTransaction();
        $error     = false;

        try {

 $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            foreach ($this->_objects as $object) {

// default credits to  base  credits
$defaultCredits1 = array();
$defaultCredits1 = Mage::getSingleton('adminhtml/session')->getTotal();
$defaultCredits = $defaultCredits1['credits'];
if ($baseCurrencyCode != $currentCurrencyCode) {
  
$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));

$baseCredits = $defaultCredits/$rates[$currentCurrencyCode];

    }
else{
     $baseCredits = $defaultCredits;
 }

//
$object->setRefundedstorecreditAmount($defaultCredits);
$object->setBaseRefundedstorecreditAmount($baseCredits);
//Mage::log($object->getRefundedstorecreditAmount());
                $object->save();
            }
        } catch (Exception $e) {
            $error = $e;
        }

        if ($error === false) {
            try {
                $this->_runCallbacks();
            } catch (Exception $e) {
                $error = $e;
            }
        }

        if ($error) {
            $this->_rollbackTransaction();
            throw $error;
        } else {
if(Mage::getSingleton('adminhtml/session')->getErr() == 'true')
         {
             Mage::getSingleton('adminhtml/session')->unsErr();
               Mage::throwException('Store Credit Must Be Less Than Grand Total');
           }  
Mage::getSingleton('adminhtml/session')->unsTotal();
            $this->_commitTransaction();
        }

        return $this;
    }



}

