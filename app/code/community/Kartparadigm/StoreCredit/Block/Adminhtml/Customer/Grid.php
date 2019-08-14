<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
public function _prepareMassaction()
    {

 parent::_prepareMassaction();
 $this->getMassactionBlock()->addItem('my_action', array(
             'label'    => Mage::helper('customer')->__('Send Credits'),
             'url'      => $this->getUrl('*/*/sendCredits')
        ));


 }

}
