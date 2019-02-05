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
class Apptha_Invitefriends_Block_Adminhtml_Invitefriends_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'invitefriends';
        $this->_controller = 'adminhtml_invitefriends';
        
        $this->_updateButton('save', 'label', Mage::helper('invitefriends')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('invitefriends')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('invitefriends_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'invitefriends_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'invitefriends_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('invitefriends_data') && Mage::registry('invitefriends_data')->getId() ) {
            return Mage::helper('invitefriends')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('invitefriends_data')->getTitle()));
        } else {
            return Mage::helper('invitefriends')->__('Add Item');
        }
    }
}