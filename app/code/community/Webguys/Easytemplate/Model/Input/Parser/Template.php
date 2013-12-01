<?php

/**
 * Class Webguys_Easytemplate_Model_Input_Parser_Template
 *
 * @method setCategory
 * @method getCategory
 *
 */
class Webguys_Easytemplate_Model_Input_Parser_Template
 extends Webguys_Easytemplate_Model_Input_Parser_Abstract
{

    public function isEnabled()
    {
        $enabled = $this->getData('enabled');
        return empty($enabled) ? true : (bool)$enabled;
    }

    public function getLabel()
    {
        return $this->getData('label');
    }

    public function getComment()
    {
        return $this->getData('comment');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getTemplate()
    {
        return $this->_getAttribute('template');
    }

    public function getType()
    {
        return $this->_getAttribute('type');
    }

    protected function _getAttribute($name)
    {
        return $this->getConfig()->getNode()->getAttribute($name);
    }

    /**
     * Orders field by sort_order attribute
     *
     * @param $a Webguys_Easytemplate_Model_Input_Parser_Field
     * @param $b Webguys_Easytemplate_Model_Input_Parser_Field
     */
    private function orderFields($a, $b)
    {
        if ($a->getSortOrder() == $b->getSortOrder()) {
            return 0;
        }
        return ($a->getSortOrder() < $b->getSortOrder()) ? -1 : 1;
    }

    /**
     * @return Webguys_Easytemplate_Model_Input_Parser_Field[]
     */
    public function getFields()
    {
        $fields = $this->getConfig()->getNode('fields');
        $result = array();

        foreach( $fields->children() AS $data )
        {
            /** @var $parser Webguys_Easytemplate_Model_Input_Parser_Field */
            $parser = Mage::getModel('easytemplate/input_parser_field');
            $parser->setConfig( $data );
            $parser->setTemplate( $this );

            $result[] = $parser;
        }

        // Sort fields by sort_order
        usort($result, array($this, 'orderFields'));

        return $result;
    }

    public function getCode()
    {
        return $this->getCategory() .'_'.parent::getCode();
    }

}