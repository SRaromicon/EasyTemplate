<?php

/**
 * Class Webguys_Easytemplate_Model_Observer
 *
 */
class Webguys_Easytemplate_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Adds view mode to cms_page and cms_block
     *
     * @param $observer
     */
    public function adminhtml_cms_prepare_form($observer)
    {
        $form = $observer->getForm();

        foreach ($form->getElements() as $element) {
            if ($element instanceof Varien_Data_Form_Element_Fieldset) {

                /** @var $sourceModel Webguys_Easytemplate_Model_Config_Source_Cms_Page_Viewmode */
                $sourceModel = Mage::getModel('easytemplate/config_source_cms_page_viewmode');

                /** @var $element Varien_Data_Form_Element_Fieldset */
                $element->addField('view_mode', 'select', array(
                    'label'     => Mage::helper('easytemplate')->__('Mode'),
                    'title'     => Mage::helper('easytemplate')->__('View Mode'),
                    'name'      => 'view_mode',
                    'required'  => true,
                    'options'   => $sourceModel->toArray(),
                    'note'      => Mage::helper('easytemplate')->__('Use the template engine or default behavior'),
                    'disabled'  => false,
                ));
            }
        }
    }

    public function cms_page_save_commit_after($observer)
    {
        /** @var $page Mage_Cms_Model_Page */
        $page = $observer->getDataObject();

        /** @var $group Webguys_Easytemplate_Model_Group */
        $group = Mage::helper('easytemplate/page')->getGroupByPageId( $page->getId() );

        /** @var $helper Webguys_Easytemplate_Helper_Data */
        $helper = Mage::helper('easytemplate');
        $helper->saveTemplateInformation($group);
    }

    public function cms_block_save_commit_after($observer)
    {
        /** @var $block Mage_Cms_Model_Block */
        $block = $observer->getDataObject();

        /** @var $group Webguys_Easytemplate_Model_Group */
        $group = Mage::helper('easytemplate/block')->getGroupByBlockId( $block->getId() );

        /** @var $helper Webguys_Easytemplate_Helper_Data */
        $helper = Mage::helper('easytemplate');
        $helper->saveTemplateInformation($group);
    }

    public function core_block_abstract_to_html_after($observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        /** @var $transport Varien_Object */
        $transport = $observer->getTransport();

        if ($block instanceof Mage_Cms_Block_Page ) {

            /** @var $block Mage_Cms_Block_Page  */
            $pageId = $block->getPage()->getId();

            if ($pageId !== false && Mage::helper('easytemplate/page')->isEasyTemplatePage($pageId)) {
                $html = '';

                /** @var $helper Webguys_Easytemplate_Helper_Page */
                $helper = Mage::helper('easytemplate/page');

                if ( $groupId = $helper->getGroupByPageId( $pageId ) ) {
                    /** @var $renderer Webguys_Easytemplate_Block_Renderer */
                    $renderer = Mage::app()->getLayout()->createBlock('easytemplate/renderer');
                    $renderer->setGroupId( $groupId );
                    $html = $renderer->toHtml();
                }

                $transport->setHtml( $html );
            }

        } elseif ($block instanceof Mage_Cms_Block_Block) {

            /** @var $block Mage_Cms_Block_Block  */
            $blockId = $block->getBlockId();

            if ($blockId && Mage::helper('easytemplate/block')->isEasyTemplateBlock($blockId)) {
                $html = '';

                /** @var $helper Webguys_Easytemplate_Helper_Block */
                $helper = Mage::helper('easytemplate/block');

                if ( $groupId = $helper->getGroupByBlockId( $blockId ) ) {
                    /** @var $renderer Webguys_Easytemplate_Block_Renderer */
                    $renderer = Mage::app()->getLayout()->createBlock('easytemplate/renderer');
                    $renderer->setGroupId( $groupId );
                    $html = $renderer->toHtml();
                }

                $transport->setHtml( $html );
            }
        }

    }

    public function adminhtml_block_html_before($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Cms_Page_Grid ||
            $block instanceof Mage_Adminhtml_Block_Cms_Block_Grid) {

            /** @var $sourceModel Webguys_Easytemplate_Model_Config_Source_Cms_Page_Viewmode */
            $sourceModel = Mage::getModel('easytemplate/config_source_cms_page_viewmode');

            $block->addColumnAfter(
                'view_mode',
                array(
                    'header' => Mage::helper('easytemplate')->__('Mode'),
                    'index' => 'view_mode',
                    'width' => '100px',
                    'header_css_class' => 'view_mode',
                    'sortable' => true,
                    'type' => 'options',
                    'options'  => $sourceModel->toArray(false),
                ),
                'root_template'
            );

        }
    }

    public function adminhtml_catalog_category_tabs( $observer )
    {
        /** @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs = $observer->getTabs();

        $tabs->addTab('easytemplate', array(
            'label'     => Mage::helper('catalog')->__('Template'),
            'content'   => $tabs->getLayout()->getBlock('adminhtml_category_templates')->toHtml(),
        ));

    }

}