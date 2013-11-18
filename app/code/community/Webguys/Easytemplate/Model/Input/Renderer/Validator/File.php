<?php

/**
 * Class Webguys_Easytemplate_Model_Input_Renderer_Validator_File
 *
 */
class Webguys_Easytemplate_Model_Input_Renderer_Validator_File extends Webguys_Easytemplate_Model_Input_Renderer_Validator_Base
{
    protected $_deleteFile = false;

    public function prepareForSave($data)
    {
        $this->_deleteFile = (bool)$data['delete'];
        return str_replace(array(' ', ',', '.'), '_', strtolower($data['value']));
    }

    public function beforeFieldSave($template, $backendModel, $field, $value)
    {
        parent::beforeFieldSave($template, $backendModel, $field, $value);

        /** @var $fileHelper Webguys_Easytemplate_Helper_File */
        $fileHelper = Mage::helper('easytemplate/file');

        $fileHelper->createTmpPath($template->getGroupId(), $template->getId());

        $uploaderData = array(
            'tmp_name' => $this->extractFilePostInformation('tmp_name', $template->getId(), $field->getCode()),
            'name' => $value
        );

        $uploader = new Mage_Core_Model_File_Uploader($uploaderData);
        //$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','pdf'));
        $uploader->addValidateCallback('easytemplate_template_file',
            $fileHelper, 'validateUploadFile');
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($fileHelper->getDestinationFilePath($template->getGroupId(), $template->getId()));

        Mage::dispatchEvent('easytemplate_upload_file_after', array(
            'result' => $result
        ));

        return $this;
    }

    protected function extractFilePostInformation($type, $templateId, $fieldCode)
    {
        return $_FILES['template'][$type][$templateId]['fields'][$fieldCode];
    }

}
