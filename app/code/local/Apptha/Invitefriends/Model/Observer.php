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
class Apptha_Invitefriends_Model_Observer {
    /* function to get session value */

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    /* function to get friend id based on customer id */

    public function getFriend($ref) {
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write'); //get db connection
        $customerTable = $resource->getTableName('apptha_invitefriends_customer');
        $selectResult = $write->query("select customer_id from $customerTable where customer_email = '$ref'");
        $customerId = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $customerId;
    }

    /* function to store registered customer */

    public function customerSaveAfter($param) {
        $customer = $param->getCustomer();
        $ref = Mage::getModel('core/cookie')->get('ref');
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            if (!empty($ref)) {
                Mage::helper('invitefriends')->insertNewCustomer($ref, $customer);
                Mage::getModel('core/cookie')->delete('ref');
            }
        }
    }

    /* function to save transaction history */

    public function storeTransactionHistory($historydata) {
        $model = Mage::getModel('invitefriends/transactionhistory');
        $model->setData($historydata);
        $model->save();
        $Customer_id = $historydata['customer_id'];
        $friend_id = $historydata['friend_id'];

        $update_bonus = '';
        if (isset($historydata['credit_amount'])) {
            $cur_balance_bonus = Mage::helper('invitefriends')->calcUserBonus($friend_id); 
            $purchase_credit = $historydata['credit_amount'] + $cur_balance_bonus;
            $update_bonus = ",bonus_flag='$purchase_credit'";
        }

        $calcTotal = Mage::helper('invitefriends')->calcTotalCredit($friend_id);
        $calcBalance =Mage::helper('invitefriends')->calcBalance($friend_id);
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $customertableName = $_customer->getTable('customer');
        $sql = "UPDATE $customertableName SET credit_amount='$calcBalance' $update_bonus WHERE friend_id = '$friend_id'";
        $write->query($sql);
    }

    public function storeBonus($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $customertableName = $_customer->getTable('customer');
        $calcUserBonus = Mage::helper('invitefriends')->calcUserBonus($friend_id);
        $calcUserBonus = round($calcUserBonus);

        $every_purchase = Mage::getStoreConfig('invitefriends/invitefriends_enable/every_purchase');
        if ($calcUserBonus && $calcUserBonus >= $every_purchase) {

            $calbonus = $calcUserBonus / $every_purchase;
            $floorvalue = floor($calbonus);

            if ($floorvalue) {
                $bonus = Mage::getStoreConfig('invitefriends/invitefriends_enable/bonus_credit');

                $bonusAmount = $bonus * $floorvalue;
                $updatebonus = $calcUserBonus - ($floorvalue * $every_purchase);

                $sql1 = "UPDATE $customertableName SET bonus_flag='$updatebonus' WHERE friend_id = '$friend_id'";
                $write->query($sql1);
                
                $bonusdata = array('friend_id' => $friend_id, 'bonus_amount' => $bonusAmount, 'status' => 1);
                $model = Mage::getModel('invitefriends/transactionhistory');
                $model->setData($bonusdata);
                $model->save();
                $calcBalance = Mage::helper('invitefriends')->calcBalance($friend_id);
                $sql2 = "UPDATE $customertableName SET credit_amount='$calcBalance' WHERE friend_id = '$friend_id'";
                $write->query($sql2);
            }
        }
       
    }

    /* function to save transaction history */

    public function saveTransactionHistory($data) {
        $model = Mage::getModel('invitefriends/invitefriends');
        /* inserting and updating follow up customer */
        $model->setData($data)
                ->setId($id);
        if ($model->getCreatedDate() == NULL || $model->getUpdatedDate() == NULL) {
            $model->setCreatedDate(now());
            $model->setUpdatedDate(now());
        } else {
            $model->setUpdatedDate(now());
        }
        $model->save();
    }

    /* function to get friends count of a particular customer */

    public function getFriendscount($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "SELECT count('friend_id') FROM $tableName WHERE friend_id = '$friend_id' and status=0";
        $selectResult = $write->query($sql);
        $invitesCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $invitesCount;
    }

    /* fucntion to update friend status after money credit */

    public function updateFriendstatus($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "UPDATE $tableName SET status=1 WHERE friend_id = '$friend_id'";
        $write->query($sql);
    }

    /* function to check number of invites */

    public function checkNumberofInvites($friend_id) {
        $_customer = Mage::getModel('invitefriends/customer')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('customer');
        $sql = "SELECT count('friend_id') FROM $tableName WHERE friend_id = '$friend_id'";
        $selectResult = $write->query($sql);
        $invitesCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $invitesCount;
    }

    public function getPurchasecount($customerId) {
        $_customer = Mage::getModel('invitefriends/invitefriends')->getCollection();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $_customer->getTable('invitefriends');
        $sql = "SELECT count('history_id') FROM $tableName WHERE type_of_transaction = 3 and customer_id=$customerId";
        $selectResult = $write->query($sql);
        $purchaseCount = $selectResult->fetch(PDO::FETCH_COLUMN);
        return $purchaseCount;
    }

    /* function to get discount */

    public function getDiscountAmount() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId();
        if ($customerId) {
            $discountAmount = Mage::helper('invitefriends')->calcBalance($customerId);
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($discountAmount > $quote->getBaseSubtotal()) {
            $discountAmount = $quote->getBaseSubtotal();
        }
        Mage::getModel('core/cookie')->set('socialdiscountAmount', $discountAmount);
        return $discountAmount;
    }

    /* Function to update discount */

    public function setdiscountamount($observer) {
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            #check no disount
            $no_discount = Mage::getModel('core/cookie')->get('no_discount');
            if ($no_discount) {
                return false;
            }
            /* get quote item */
            $quote = $observer->getEvent()->getQuote();
            $discountAmount = $this->getDiscountAmount();
            if ($discountAmount) {
                //we calculate the Ratio of taxes between GrandTotal & Discount Amount to know how tach we need to remove.
                $rat = 1 - ($discountAmount / $quote->getGrandTotal());
                $tax = $quote->getGrandTotal() - $quote->getSubtotal();
                $tax = $tax * $rat;
                $discountAmountWithoutTax = $discountAmount - $tax;
                $total = $quote->getGrandTotal();
                $quote->setGrandTotal($quote->getGrandTotal() - $discountAmount)
                        ->setBaseGrandTotal($quote->getBaseGrandTotal() - $discountAmount)
                        ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmountWithoutTax)
                        ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmountWithoutTax)
                        ->save();
                $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                foreach ($quote->getAllAddresses() as $address) {
                    /* Set Subtotal */
                    $address->setSubtotal(0);
                    $address->setBaseSubtotal(0);
                    /* Set Grand total */
                    $address->setGrandTotal(0);
                    $address->setBaseGrandTotal(0);
                    $address->collectTotals();
                    if ($address->getAddressType() == $canAddItems) {
                        $address->setSubtotal((float) $quote->getSubtotal());
                        $address->setBaseSubtotal((float) $quote->getBaseSubtotal());
                        $address->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount());
                        $address->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount());
                        $address->setGrandTotal((float) $quote->getGrandTotal());
                        $address->setBaseGrandTotal((float) $quote->getBaseGrandTotal());
                        $address->setDiscountAmount($address->getDiscountAmount() + (-$discountAmount));
                        /* Set Discount Description */
                        if ($address->getDiscountDescription()) {

                            $title = 'Affiliates Amount+ ' . $address->getDiscountDescription();
                        } else {
                            $title = 'Affiliates Amount';
                        }
                        /* Set Discount Amount */
                        $address->setDiscountDescription($title);
                        $address->setBaseDiscountAmount($address->getBaseDiscountAmount() + (-$discountAmount));
                        $address->save();
                    }//end: if
                } //end: foreach
                foreach ($quote->getAllItems() as $item) {
                    //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                    $rat = $item->getBaseRowTotal() / $total;
                    $ratdisc = $discountAmount * $rat;
                    $item->setDiscountAmount($ratdisc);
                    $item->setBaseDiscountAmount($ratdisc);
                } //end: foreach
            }
        }
    }

    public function placeAfter($observer) {
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            $orderIds = $observer->getEvent()->getOrderIds();
            $order = Mage::getModel('sales/order')->load($orderIds[0]);
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $_customer = Mage::getModel('invitefriends/customer')->load($customer->getId());
            if ($customer->getId()) {
                $_customer = Mage::getModel('invitefriends/customer')->load($customer->getId());
                $get_Friendid = $customer->getId();
                //Subtract dicount of customer and save in order
                $no_discount = Mage::getModel('core/cookie')->get('no_discount');
                if (!$no_discount) {
                    $discountAmount = Mage::getModel('core/cookie')->get('socialdiscountAmount');
                    if ($discountAmount) {
                        $getCustomerId = $_customer->getCustomerId();
                        $historyData = array('customer_id' => $get_CustomerId, 'friend_id' => $get_Friendid, 'debit_amount' => $discountAmount, 'order_id' => $order->getIncrementId(), 'status' => 1);
                        $this->storeTransactionHistory($historyData);
                        Mage::getModel('core/cookie')->delete('socialdiscountAmount');
                    }
                }
                #store transaction history
                //friend ID
                $getFriendid = $_customer->getFriendId();
                if (!empty($getFriendid)) {
                    $getCustomerId = $_customer->getCustomerId();
                    $purchaseCredit = Mage::helper('invitefriends')->getPurchaseCredit($order->getGrandTotal());
                    if ($purchaseCredit) {
                        $historyData = array('customer_id' => $getCustomerId, 'friend_id' => $getFriendid, 'credit_amount' => $purchaseCredit, 'order_id' => $order->getIncrementId(), 'status' => 1);
                        $this->storeTransactionHistory($historyData);
                    }
                    $calcUserBonus = Mage::helper('invitefriends')->calcUserBonus($getFriendid);
                    $every_purchase = Mage::getStoreConfig('invitefriends/invitefriends_enable/every_purchase');
                    $this->storeBonus($getFriendid);
                }
            }
        }
    }

    public function saveOrderInvoiceAfter($argv) {
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            $invoice = $argv->getInvoice();
            $order = $invoice->getOrder();
            $customerId = $order->getCustomerId();
            $orderId = $order->getIncrementId();
            $customer = Mage::getModel('invitefriends/customer')->load($customerId);
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $_history = Mage::getModel('invitefriends/invitefriends')->getCollection();
            $tableName = $_history->getTable('invitefriends');
            if ($customer->getCreditAmount() != 0) {
                $selectResult = $write->query("SELECT history_id,amount FROM $tableName WHERE customer_id=$customerId and transaction_detail=$orderId");
                $transactions = $selectResult->fetch(PDO::FETCH_ASSOC);
                $creditAmount = $customer->getCreditAmount() - $transactions['amount'];
                $historyId = $transactions['history_id'];
                $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
                $customer->setCreditAmount($creditAmount);
                $customer->save();
                $status = Apptha_Invitefriends_Model_Status::COMPLETE;
                $transaction->setBalance($customer->getCreditAmount())->setTransactionTime(now());
                $transaction->setStatus($status)->save();
            }
            $typeofTransaction = Apptha_Invitefriends_Model_Type::FRIEND_PURCHASE;
            $statusCheck = Apptha_Invitefriends_Model_Status::PENDING;
            $friendId = $customer->getFriendId();
            $transactionDetails = $customerId . '|' . $orderId;
            $selectResult = $write->query("SELECT history_id FROM $tableName WHERE customer_id=$friendId and type_of_transaction=$typeofTransaction and status=$statusCheck and transaction_detail='$transactionDetails'");
            $historyId = $selectResult->fetch(PDO::FETCH_COLUMN);
            $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
            $status = Apptha_Invitefriends_Model_Status::PROCESSING;
            $transaction->setStatus($status)->save();
        }
    }

    public function paymentCancel($arvgs) {
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            $payment = $arvgs->getPayment();
            $order = $payment->getOrder();
            $customerId = $order->getCustomerId();
            $orderId = $order->getIncrementId();
            $customer = Mage::getModel('invitefriends/customer')->load($customerId);
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $_history = Mage::getModel('invitefriends/invitefriends')->getCollection();
            $tableName = $_history->getTable('invitefriends');
            $selectResult = $write->query("SELECT history_id,amount FROM $tableName WHERE customer_id=$customerId and transaction_detail=$orderId");
            $transactions = $selectResult->fetch(PDO::FETCH_ASSOC);
            $historyId = $transactions['history_id'];
            $transaction = Mage::getModel('invitefriends/invitefriends')->load($historyId);
            $status = Apptha_Invitefriends_Model_Status::UNCOMPLETE;
            $transaction->setBalance($customer->getCreditAmount())->setTransactionTime(now());
            $transaction->setStatus($status)->save();
        }
    }

    /* function to update credits for friend purchase */

    public function updateFriendpurchase() {
        if (Mage::helper('invitefriends')->isInvitefriendsEnabled()) {
            $transactions = Mage::getModel('invitefriends/invitefriends')->getCollection()
                    ->addFieldToFilter('status', Apptha_Invitefriends_Model_Status::PROCESSING)
                    ->addFieldToFilter('type_of_transaction', 3)
                    ->addOrder('history_id', 'ASC');

            foreach ($transactions as $transaction) {
                $date = date('Y-m-d');
                $transactionTime = $transaction->getTransactionTime();
                $status = $transaction->getStatus();
                $transactionDate = date('Y-m-d', strtotime($transactionTime));
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $selectResult = $write->query("SELECT DATEDIFF('$date','$transactionDate') AS DiffDate");
                $days = $selectResult->fetch(PDO::FETCH_COLUMN);
                if (($days >= Mage::helper('invitefriends')->getLimitationDays()) && ($status == Apptha_Invitefriends_Model_Status::PROCESSING)) {
                    $customerId = $transaction->getCustomerId();
                    $customerDetails = Mage::getModel('invitefriends/customer')->load($customerId);
                    $customerDetails->setCreditAmount($customerDetails->getCreditAmount() + $transaction->getAmount())->save();
                    $status = Apptha_Invitefriends_Model_Status::COMPLETE;
                    $transaction->setBalance($customerDetails->getCreditAmount())->setTransactionTime(now());
                    $transaction->setStatus($status)->save();
                }
            }
        }
    }

    public function paramRef($observer) {
        #set ref email 
        $ref = (string) Mage::app()->getRequest()->getParam('ref');
        if ($ref) {
            Mage::getModel('core/cookie')->set('ref', $ref);
            if (Zend_Validate::is($ref, 'EmailAddress')) {
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {  // if not logged in
                    Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('', array('_secure' => true)) . "invitefriends/index");
                    Mage::getSingleton('checkout/session')->addSuccess("Your referral link accepted successfully!");
                    //redirect to login page
                    $url = Mage::getUrl('', array('_secure' => true)). 'customer/account/login';
                    $response = Mage::app()->getFrontController()->getResponse();
                    $response->setRedirect($url);
                    $response->sendResponse();
                } else {
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    Mage::helper('invitefriends')->insertNewCustomer($ref, $customer);
                    //redirect to login page
                    $url = Mage::getUrl('', array('_secure' => true)) . 'invitefriends/index';
                    $response = Mage::app()->getFrontController()->getResponse();
                    $response->setRedirect($url);
                    $response->sendResponse();
                }
            }
        }
    }

}

?>
