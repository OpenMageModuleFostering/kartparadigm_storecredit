<?php
class Kartparadigm_StoreCredit_Block_Adminhtml_Registries1_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{
 public function __construct()
    {
       parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
/*

$collection = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection();
$collection->getSelect()->columns(array('id' => 'MAX(t_id)'))->group(array('c_id'));
$collection->getSelect()->join(array('refer' => 'customer_entity'),'refer.entity_id = main_table.c_id');



*/

  $collection1 = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection();
$collection1->getSelect()->columns('MAX(t_id)')->group(array('c_id'));
//$select = $collection1->getSelect();
$arr1 = array();
$i = 0;
//if($collection1 > 0) {
foreach($collection1  as $s)
$arr1[$i++] = $s['MAX(t_id)'];
if(count($arr1) > 0) {

     $collection = Mage::getModel('customer/customer')->getCollection()
                  ->addNameToSelect()
                  ->joinTable('kartparadigm_storecredit/creditinfo', 'c_id=entity_id', array('*'), "t_id IN (".implode(',',$arr1).")", 'right');

     //$collection->joinAttribute('t_id', array('in' => $select))->group(array('c_id'));
      $collection->getSelect()->columns('MAX(t_id) as t_id')->group(array('c_id'));
    $collection->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left'); 

 
        $this->setCollection($collection);
}
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        /*$this->addColumn('firstname', array(
            'header'    => Mage::helper('customer')->__('First Name'),
            'index'     => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('Last Name'),
            'index'     => 'lastname'
        ));*/
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

      /*  $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));*/

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));
 $model = Mage::getModel('kartparadigm_storecredit/creditinfo')->getCollection()->addFieldToFilter('t_id');
          $this->addColumn('total_credits', array(
            'header'    => Mage::helper('customer')->__('current credits'),
            'align'     => 'center',
            'index'     => 'total_credits',
            'gmtoffset' => true
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> 'adminhtml/customer/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('adminhtml/customer/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('adminhtml/customer/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

    
         $this->getMassactionBlock()->addItem('my_action', array(
             'label'    => Mage::helper('customer')->__('Send Credits'),
             'url'      => $this->getUrl('*/customer/sendCredits')
        ));

        

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/customer/edit', array('id'=>$row->getId()));
    }
}

