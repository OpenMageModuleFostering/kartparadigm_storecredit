<?php
class
Kartparadigm_StoreCredit_Block_Adminhtml_Customer_Edit_Tab_Credits_List1 extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct(){
parent::__construct();
$this->setId('cardsGrid');
$this->setDefaultSort('total_credits');
$this->setDefaultDir('DESC');
$this->setSaveParametersInSession(true);
$this->setPagerVisibility(false);
$this->setFilterVisibility(false);
}
protected function _prepareCollection(){
$collection1;
$col2 = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('c_id',$this->getRequest()->getParam('id'))->setOrder('t_id','desc')->addFieldToFilter('website1','Main Website')->getLastItem();
foreach($col2 as $col)
$collection1 = $col;
$this->setCollection($col2);
return parent::_prepareCollection();
}
protected function _prepareColumns()
{
$this->addColumn('total_credits', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Store Credits'),
'index' => 'total_credits',
'sortable' => false,
));
$this->addColumn('website1', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Website'),
'index' => 'website1',
'sortable' => false,
));
return parent::_prepareColumns();
}

}

