<?php

/**
 *
 *
 */

class Webguys_Easytemplate_Block_Adminhtml_Category_Templates
    extends Webguys_Easytemplate_Block_Adminhtml_Edit_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * @return Webguys_Easytemplate_Model_Group
     */
    public function getGroup()
    {
        if ($category = $this->getObjectOfType()) {
            return Mage::helper('easytemplate/page')->getGroupByPageId( $category->getId() );
        }
        return Mage::getModel('easytemplate/group');
    }

    public function getType()
    {
        return 'category';
    }

    public function getObjectOfType()
    {
        return Mage::registry('category');
    }

}