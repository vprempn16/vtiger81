<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_MappingModal_View extends Settings_Vtiger_IndexAjax_View
{

    public function process(Vtiger_Request $request)
    {
        $qualifiedModuleName = $request->getModule(false);
        $templateId = $request->get('template_id');

        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_whatsapp_templates WHERE id = ?', array($templateId));
        $templateData = array();
        if ($db->num_rows($result)) {
            $templateData = $db->query_result_rowdata($result, 0);
        }

        $components = json_decode($templateData['components'], true);
        $variables = array();

        if (is_array($components)) {
            foreach ($components as $component) {
                // Look for text attributes that might contain variables like {{1}}
                if (isset($component['text'])) {
                    preg_match_all('/\{\{(\d+)\}\}/', $component['text'], $matches);
                    if (!empty($matches[1])) {
                        foreach ($matches[1] as $match) {
                            $variables[] = array(
                                'name' => '{{' . $match . '}}',
                                'type' => $component['type']
                            );
                        }
                    }
                }
            }
        }

        // Fetch all entity modules
        $modules = Vtiger_Module_Model::getEntityModules();
        $mappedModule = $templateData['module'];

        // Fetch existing mappings
        $mappings = array();
        if (!empty($templateData['id'])) {
            $mapResult = $db->pquery('SELECT * FROM vtiger_whatsapp_template_map WHERE template_id = ?', array($templateData['id']));
            for ($i = 0; $i < $db->num_rows($mapResult); $i++) {
                $row = $db->query_result_rowdata($mapResult, $i);
                $mappings[$row['template_variable']] = $row;
            }
        }

        // Fetch fields if a module is already mapped
        $moduleFields = array();
        if (!empty($mappedModule)) {
            $moduleModel = Vtiger_Module_Model::getInstance($mappedModule);
            if ($moduleModel) {
                $fields = $moduleModel->getFields();
                foreach ($fields as $fieldName => $fieldModel) {
                    if ($fieldModel->isViewable()) {
                        $moduleFields[$fieldName] = vtranslate($fieldModel->get('label'), $mappedModule);
                    }
                }
            }
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('TEMPLATE_ID', $templateId);
        $viewer->assign('META_TEMPLATE_ID', $templateData['template_id']);
        $viewer->assign('TEMPLATE_NAME', $templateData['template_name']);
        $viewer->assign('TEMPLATE_LANGUAGE', $templateData['language']);
        $viewer->assign('VARIABLES', $variables);
        $viewer->assign('MODULES', $modules);
        $viewer->assign('MAPPED_MODULE', $mappedModule);
        $viewer->assign('MAPPINGS', $mappings);
        $viewer->assign('MODULE_FIELDS', $moduleFields);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

        $viewer->view('MappingModal.tpl', $qualifiedModuleName);
    }
}
