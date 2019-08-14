<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct(){
parent::__construct();
$this->setId('cardsGrid');
$this->setDefaultSort('t_id');
$this->setDefaultDir('ASC');
$this->setSaveParametersInSession(true);
}
protected function _prepareCollection(){
$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection();
$collection->getSelect()->join(array('refer' => 'customer_entity'),'refer.entity_id = main_table.c_id');
$this->setCollection($collection);
return parent::_prepareCollection();
}
protected function _prepareColumns()
{
$this->addColumn('c_id', array(
'header' => Mage::helper('kartparadigm_storecredit')->__('Customer Id'),
'index'=> 'c_id',
'sortable' => false,
));
$this->addColumn('email', array(
'header' => Mage::helper('kartparadigm_storecredit')->__('Customer Email'),
'index'=> 'email',
'sortable' => false,
));
$this->addColumn('action', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Action On Credits'),
'index' => 'action',
'sortable' => false,
));
$this->addColumn('action credits', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Credits Added'),
'index' => 'action_credits',
'sortable' => false,
));
$this->addColumn('credits', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Total Credits'),
'index' => 'total_credits',
'sortable' => false,
));
$this->addColumn('action_date', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Action Date'),
'index' => 'action_date',
'sortable' => false,
));
$this->addColumn('custom_msg', array(
'header'=> Mage::helper('kartparadigm_storecredit')->__('Message'),
'index' => 'custom_msg',
'sortable' => false,
));
return parent::_prepareColumns();
}

public function getRowUrl($row)
    {
        return $this->getUrl('*/*/showTransaction', array('id'=>$row->getId()));
    }
}

