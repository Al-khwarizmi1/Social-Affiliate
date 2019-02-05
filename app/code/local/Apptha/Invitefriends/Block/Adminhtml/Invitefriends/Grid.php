<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_SocialAffiliate
 * @version     0.1.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
class Apptha_Invitefriends_Block_Adminhtml_Invitefriends_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('invitefriendsGrid');
      $this->setDefaultSort('friend_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('invitefriends/customer')->getCollection();
      $collection->getSelect()->group('friend_id');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('friend_id', array(
          'header'    => Mage::helper('invitefriends')->__('Customer ID'),
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'friend_id',
      ));

//      $this->addColumn('customer_name', array(
//          'header'    => Mage::helper('invitefriends')->__('Customer Name'),
//          'align'     =>'left',
//          'index'     => 'customer_name',
//      ));

      $this->addColumn('friend_email', array(
          'header'    => Mage::helper('invitefriends')->__('Customer Email'),
          'align'     =>'left',
          'index'     => 'friend_email',
      ));

      $this->addColumn('credit_amount', array(
          'header'    => Mage::helper('invitefriends')->__('Credits'),
          'align'     =>'left',
          'index'     => 'credit_amount',
      ));

      $this->addColumn('created_date', array(
          'header'    => Mage::helper('invitefriends')->__('Joined Date'),
          'align'     =>'left',
          'index'     => 'created_date',
      ));


//	  
//        $this->addColumn('action',
//            array(
//                'header'    =>  Mage::helper('invitefriends')->__('Action'),
//                'width'     => '100',
//                'type'      => 'action',
//                'getter'    => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('invitefriends')->__('View'),
//                        'url'       => array('base'=> 'adminhtml/customer/edit'),
//                        'field'     => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//                'is_system' => true,
//        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('invitefriends')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('invitefriends')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
//        $this->setMassactionIdField('customer_id');
//        $this->getMassactionBlock()->setFormFieldName('invitefriends');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('invitefriends')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('invitefriends')->__('Are you sure?')
//        ));
//        
//        return $this;
    }

  public function getRowUrl($row)
  {
       //return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getId()));
  }

}