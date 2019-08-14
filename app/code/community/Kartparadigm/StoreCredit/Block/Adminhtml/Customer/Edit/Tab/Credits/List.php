<?php
class
Kartparadigm_StoreCredit_Block_Adminhtml_Customer_Edit_Tab_Credits_List extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct(){
parent::__construct();
$this->setId('cardsGrid');
$this->setDefaultSort('t_id');
$this->setDefaultDir('DESC');
$this->setSaveParametersInSession(true);
//$this->setFilterVisibility(false);
}
protected function _prepareCollection(){
$col = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$this->getRequest()->getParam('id'))->setOrder('t_id','desc');
$this->setCollection($col);
return parent::_prepareCollection();
}
protected function _prepareColumns()
{
$this->addColumn('action_date', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Date'),
'index' => 'action_date',
'sortable' => true,
'filter' => false,
));
$this->addColumn('My_Website', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Website'),
'index' => 'website1',
'sortable' => false,
'type'    => 'options',
'options'=> Mage::getModel('kartparadigm_storecredit/creditinfo')->getWebsites(),
));
$this->addColumn('action', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Action'),
'index' => 'action',
'sortable' => false,
'type'    => 'options',
'options' => array('Credited ' => $this->__('Created'), 'Updated' => $this->__('Updated'), 'Used' => $this->__('Used'), 'Refunded' =>$this->__('Refunded'), 'Deducted' => $this->__('Deducted'), 'Crdits' => $this->__('Crdits')),
));
$this->addColumn('action credits', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Balance Change'),
'index' => 'action_credits',
'sortable' => false,
'filter' => false,
));
$this->addColumn('credits', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Balance'),
'index' => 'total_credits',
'sortable' => false,
'filter' => false,
));
$this->addColumn('customer_notification_status', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Customer notified?'),
'index' => 'customer_notification_status',
'sortable' => false,
'filter' => false,
));

$this->addColumn('custom_msg', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Additional Information'),
'index' => 'custom_msg',
'sortable' => false,
));
return parent::_prepareColumns();
}

}

