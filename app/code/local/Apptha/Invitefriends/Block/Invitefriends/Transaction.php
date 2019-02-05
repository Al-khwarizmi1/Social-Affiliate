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
class Apptha_Invitefriends_Block_Invitefriends_Transaction extends Mage_Core_Block_Template
{

    protected function _prepareLayout()
    {
		$this->setToolbar($this->getLayout()->createBlock('page/html_pager','invitefriends_transaction_toolbar'));
		$this->getToolbar()->setCollection($this->_getTransaction());
    }
	protected function _getCustomer()
	{
		return Mage::getSingleton("customer/session")->getCustomer();
	}
	
	public function _getTransaction()
	{
		if($this->getPageSize()) 
               $pagesize = $this->getPage_size();
		$transactions = Mage::getModel('invitefriends/transactionhistory')->getCollection()
						->addFieldToFilter('friend_id',$this->_getCustomer()->getId())
						->addOrder('transaction_history_id','ASC');
		return $transactions;
	}
	
	public function getTransaction()
	{
		return $this->getToolbar()->getCollection();
	}
	
	public function getTypeLabel($type)
	{
		return Apptha_Invitefriends_Model_Type::getLabel($type);
	}
	
	public function getTransactionDetail($type, $detail=null, $status=null)
	{
		return Apptha_Invitefriends_Model_Type::getTransactionDetail($type,$detail,$status);
	}
	
	public function formatAmount($amount, $type)
	{
		return Apptha_Invitefriends_Model_Type::getAmountWithSign($amount,$type);
	}
	
	public function getPositiveAmount($amount, $type)
	{
		$result = Apptha_Invitefriends_Model_Type::getAmountWithSign($amount,$type);
		return $result>0?$result:0;
	}
	
	public function getStatusText($status)
	{
		return Apptha_Invitefriends_Model_Status::getLabel($status);
	}
	
	public function getToolbarHtml()
	{
		return $this->getToolbar()->toHtml();
	}

}