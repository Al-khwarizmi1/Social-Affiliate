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
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected static $_statuses;

    public function __construct()
    {
        self::$_statuses = array(
                null                                        => null,
                Mage_Newsletter_Model_Queue::STATUS_SENT    => Mage::helper('customer')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL  => Mage::helper('customer')->__('Cancel'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER   => Mage::helper('customer')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('customer')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE   => Mage::helper('customer')->__('Paused'),
            );
        parent::__construct();
    }

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>Mage::helper('customer')->__($label));
        }

        return $result;
    }

    public function getCondition()
    {
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}
