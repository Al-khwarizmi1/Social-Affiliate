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
class Apptha_Invitefriends_Helper_Data extends Mage_Core_Helper_Abstract {
    /* function to check if invite friends enabled */

    public function isInvitefriendsEnabled() {
        return Mage::getStoreConfig('invitefriends/invitefriends_enable/enable_invitefriends');
    }

    public function enabledSharelink() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_link');
    }

    public function enabledEmailInvite() {
        return Mage::getStoreConfig('invitefriends/email_settings/enable_email_invite');
    }

    public function enabledFacebookInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_fb');
    }

    public function enabledTwitterInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_twitter');
    }

    public function enabledGmailInvite() {
        return Mage::getStoreConfig('invitefriends/social_invite/enable_gmail');
    }

    public function getCreitType() {
        return Mage::getStoreConfig('invitefriends/Credits/fixed_percentage');
    }

    public function getCreditAmount() {
        return Mage::getStoreConfig('invitefriends/Credits/amount_credited');
    }

    public function getInviteCredits() {
        return Mage::getStoreConfig('invitefriends/credits_invite/amount_invite');
    }

    public function getNumberofFriends() {
        return Mage::getStoreConfig('invitefriends/credits_invite/invite_number');
    }

    public function getBonusAmount() {
        return Mage::getStoreConfig('invitefriends/bonus_invites/amount_invite');
    }

    public function getBonusInvites() {
        return Mage::getStoreConfig('invitefriends/bonus_invites/invite_number');
    }

    public function getPurchaseBonus() {
        return Mage::getStoreConfig('invitefriends/purchase_bonus/amount_bonus');
    }

    public function getLimitationDays() {
        return Mage::getStoreConfig('invitefriends/purchase_bonus/days_bonus');
    }

    public function getfbinviteUrl() {
        return $this->_getUrl('invitefriends/index/fbinvite', array('_secure' => true));
    }

    public function getfbappId() {
        return Mage::getStoreConfig('invitefriends/social_invite/fb_app');
    }

    public function getfbsecretKey() {
        return Mage::getStoreConfig('invitefriends/social_invite/fb_secret');
    }

    public function gettwitterConsumerkey() {
        return Mage::getStoreConfig('invitefriends/social_invite/twitter_app');
    }

    public function gettwitterConsumersecret() {
        return Mage::getStoreConfig('invitefriends/social_invite/twitter_secret');
    }

    public function getgmailClientid() {
        return Mage::getStoreConfig('invitefriends/social_invite/gmail_clientid');
    }

    public function getgmailClientsecretkey() {
        return Mage::getStoreConfig('invitefriends/social_invite/gmail_secretkey');
    }

    public function getfbshareTitle() {
        return Mage::getStoreConfig('invitefriends/social_invite/fbshare_title');
    }

    public function getfbshareDescription() {
        return Mage::getStoreConfig('invitefriends/social_invite/fbshare_description');
    }

    //get Invitation link of customer.
    public function getLink(Mage_Customer_Model_Customer $customer) {
        return trim(Mage::getUrl('invitefriends/index'), "/") . "?c=" . $customer->getEmail();
    }

      public function getPurchaseCredit($totals) {
           $subtotal = $totals; //Subtotal value
          $purchase_credit = Mage::getStoreConfig('invitefriends/invitefriends_enable/purchase_credit');
            $discountAmount = $purchase_credit*$subtotal / 100;
         
          
        return $discountAmount;
    }
    
    //insert customer
    public function insertNewCustomer($ref,$customer)
    {
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write'); //get db connection
        $customerTable = $resource->getTableName('apptha_invitefriends_customer');
        # Customer id
        $customerid = $customer->getId();
        $customeremail = $customer->getEmail();
        $customername = $customer->getName();
        //friend id
        $friend = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($ref);
        $friend_id = $friend->getId();
        if($friend_id != $customerid)
        {    
        $createdDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $selectresult = $write->query("select customer_id from $customerTable where customer_id	= '$customerid'");
        $customer_id = $selectresult->fetch(PDO::FETCH_COLUMN);        
        if (empty($customer_id)) {
             $sql = "INSERT INTO  $customerTable (customer_id,friend_id,customer_email,friend_email,customer_name,status,created_date) VALUES ('$customerid','$friend_id','$customeremail','$ref','$customername','0','$createdDate')";
              $write->query($sql);
            }
        }   
    }
     #total Credit
    public function calcTotalCredit($friend_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $historyTable = $resource->getTableName('apptha_transaction_history');
        $selectResult = "SELECT  
            SUM( credit_amount ) as credit 
            FROM $historyTable 
            WHERE   friend_id = '$friend_id'";
        $credit_amount = $read->fetchOne($selectResult);
        return $credit_amount;
    }   
    #total Balance
     public function calcBalance($friend_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $historyTable = $resource->getTableName('apptha_transaction_history');
        $selectResult = "SELECT  
            ( SUM( credit_amount ) + SUM( bonus_amount )) - SUM( debit_amount ) as credit 
            FROM $historyTable 
            WHERE friend_id = '$friend_id'";
        $credit_amount = $read->fetchOne($selectResult);
        return $credit_amount;
    }  
   #total debit 
     public function calcTotalDebit($friend_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $historyTable = $resource->getTableName('apptha_transaction_history');
        $selectResult = "SELECT  
            SUM( debit_amount ) as credit 
            FROM $historyTable 
            WHERE   friend_id = '$friend_id'";
        $credit_amount = $read->fetchOne($selectResult);
        return $credit_amount;
    }   
    #total Balance
     public function calcTotalBalance($friend_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $historyTable = $resource->getTableName('apptha_transaction_history');
        $selectResult = "SELECT  
            SUM( credit_amount ) + SUM( bonus_amount ) as credit 
            FROM $historyTable 
            WHERE friend_id = '$friend_id'";
        $credit_amount = $read->fetchOne($selectResult);
        return $credit_amount;
    }
    
    #total refered 
     public function calcTotalrefered()
    {
       $friend_id = Mage::getSingleton("customer/session")->getCustomer()->getId();  
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $customerTable = $resource->getTableName('apptha_invitefriends_customer');
        $selectResult = "SELECT  
            COUNT(`customer_id`) as counts
            FROM $customerTable 
            WHERE friend_id = '$friend_id'
            AND customer_id <> 0 ";
        $counts = $read->fetchOne($selectResult);
        return $counts;
    }
    
    #total refered purchase
     public function calcTotalreferedpurchase()
    {
       $friend_id = Mage::getSingleton("customer/session")->getCustomer()->getId();  
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $historyTable = $resource->getTableName('apptha_transaction_history');
        $selectResult = "SELECT  
            COUNT(  `customer_id` ) AS counts
            FROM $historyTable 
            WHERE friend_id = '$friend_id'
            AND order_id <> 0
            AND customer_id <> 0 " ;
        $counts = $read->fetchOne($selectResult);
        return $counts;
    }
      #user bonus
     public function calcUserBonus($friend_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read'); //get db connection
        $customerTable = $resource->getTableName('apptha_invitefriends_customer');
        $selectResult = "SELECT  
            bonus_flag
            FROM $customerTable 
            WHERE friend_id = '$friend_id'";
        $bon_amount = $read->fetchOne($selectResult);
        return $bon_amount;
    }      
	public function domainKey($tkey) {

		$message = "EM-IFMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";

		for ($i = 0; $i < strlen($tkey); $i++) {
			$key_array[] = $tkey[$i];
		}
		$enc_message = "";
		$kPos = 0;
		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		for ($i = 0; $i < strlen($chars_str); $i++) {
			$chars_array[] = $chars_str[$i];
		}
		for ($i = 0; $i < strlen($message); $i++) {
			$char = substr($message, $i, 1);

			$offset = $this->getOffset($key_array[$kPos], $char);
			$enc_message .= $chars_array[$offset];
			$kPos++;
			if ($kPos >= count($key_array)) {
				$kPos = 0;
			}
		}

		return $enc_message;
	}
	public function license()
	{
	 return 'license';
	}
	public function getOffset($start, $end) {

		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		for ($i = 0; $i < strlen($chars_str); $i++) {
			$chars_array[] = $chars_str[$i];
        }
        
		for ($i = count($chars_array) - 1; $i >= 0; $i--) {
			$lookupObj[ord($chars_array[$i])] = $i;
		}
		$sNum = $lookupObj[ord($start)];
		$eNum = $lookupObj[ord($end)];
		$offset = $eNum - $sNum;
		if ($offset < 0) {
			$offset = count($chars_array) + ($offset);
		}
		return $offset;
	}
}