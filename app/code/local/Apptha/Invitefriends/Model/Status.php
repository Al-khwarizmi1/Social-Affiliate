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
class Apptha_Invitefriends_Model_Status extends Varien_Object
{
    const UNCOMPLETE			= 0;
    const PENDING			= 1;		//haven't change points yet
    const COMPLETE			= 2;    
    const PROCESSING                    = 3;


    static public function getOptionArray()
    {
        return array(
            self::PENDING    		=> Mage::helper('invitefriends')->__('Pending'),
            self::COMPLETE  		=> Mage::helper('invitefriends')->__('Complete'),
            self::UNCOMPLETE	    	=> Mage::helper('invitefriends')->__('Uncomplete'),
            self::PROCESSING	    	=> Mage::helper('invitefriends')->__('Processing'),
        );
    }

    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}