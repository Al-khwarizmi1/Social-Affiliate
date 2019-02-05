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
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('apptha_invitefriends_customer')};
CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_invitefriends_customer')} (
   `customer_id` int(11) NOT NULL,
  `token_id` varchar(255) NOT NULL,
  `fbuserid` varchar(100) NOT NULL,
  `fbfriendids` text,
  `friend_id` int(11) NOT NULL,
  `friend_email` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `credit_amount` decimal(12,2) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `bonus_flag` decimal(12,2) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('apptha_transaction_history')};
CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_transaction_history')} (
  `transaction_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `credit_amount` decimal(12,2) NOT NULL,
  `debit_amount` decimal(12,2) NOT NULL,
  `bonus_amount` decimal(12,2) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_history_id`)
) ENGINE=InnoDB;

 

-- DROP TABLE IF EXISTS {$this->getTable('apptha_invitefriends_history')};
CREATE TABLE IF NOT EXISTS {$this->getTable('apptha_invitefriends_history')} (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `type_of_transaction` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance` decimal(12,2) NOT NULL,
  `transaction_detail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");  

$installer->endSetup(); 
