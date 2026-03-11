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

        $mappings = array();
        if (!empty($templateData['id'])) {
            $mapResult = $db->pquery('SELECT * FROM vtiger_whatsapp_template_map WHERE template_id = ?', array($templateData['id']));
            if ($db->num_rows($mapResult) > 0) {
                while ($row = $db->fetch_array($mapResult)) {
                    $uniqueKey = $row['component_type'] . '_' . $row['template_variable'];
                    $mappings[$uniqueKey] = $row;
                }
            }
        }

        $componentsStr = html_entity_decode($templateData['components'], ENT_QUOTES, 'UTF-8');
        $components = json_decode($componentsStr, true);

        $variables = array();
        $groupedVariables = array();

        if (is_array($components)) {
            foreach ($components as $component) {
                if (isset($component['type']) && in_array($component['type'], array('BODY', 'HEADER', 'BUTTONS'))) {
                    $type = $component['type'];

                    if ($type === 'BUTTONS' && isset($component['buttons'])) {
                        $bIndex = 1;
                        foreach ($component['buttons'] as $btn) {
                            $btnType = 'BUTTONS_' . $bIndex;
                            $btnStr = json_encode($btn);

                            if (preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $btnStr, $matches)) {
                                $uniqueVars = array_unique($matches[1]);
                                foreach ($uniqueVars as $match) {
                                    $varName = '{{' . $match . '}}';
                                    $exampleData = isset($btn['text']) ? $btn['text'] : 'Button ' . $bIndex;
                                    $contextInfo = "Button " . $bIndex . " URL Parameter";

                                    $variables[] = array(
                                        'name' => $varName,
                                        'type' => $btnType
                                    );

                                    $groupedVariables['BUTTONS'][] = array(
                                        'name' => $varName,
                                        'type' => $btnType,
                                        'key' => $match,
                                        'example' => $exampleData,
                                        'context' => $contextInfo
                                    );
                                }
                            }
                            $bIndex++;
                        }
                    } else {
                        $componentStr = json_encode($component);
                        if (preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $componentStr, $matches)) {
                            $uniqueVars = array_unique($matches[1]);

                            $exampleValues = array();
                            $namedExamples = array();

                            if ($type == 'BODY') {
                                if (isset($component['example']['body_text'][0])) {
                                    $exampleValues = $component['example']['body_text'][0];
                                } elseif (isset($component['example']['body_text_named_params'])) {
                                    foreach ($component['example']['body_text_named_params'] as $np) {
                                        $namedExamples[$np['param_name']] = $np['example'];
                                    }
                                }
                            } elseif ($type == 'HEADER') {
                                if (isset($component['example']['header_text'][0])) {
                                    $exampleValues = $component['example']['header_text'];
                                } elseif (isset($component['example']['header_text_named_params'])) {
                                    foreach ($component['example']['header_text_named_params'] as $np) {
                                        $namedExamples[$np['param_name']] = $np['example'];
                                    }
                                }
                            }

                            $varIndex = 0;
                            foreach ($uniqueVars as $match) {
                                $varName = '{{' . $match . '}}';

                                $variables[] = array(
                                    'name' => $varName,
                                    'type' => $type
                                );

                                $exampleData = '';
                                if (isset($namedExamples[$match])) {
                                    $exampleData = $namedExamples[$match];
                                } elseif (isset($exampleValues[$varIndex])) {
                                    $exampleData = $exampleValues[$varIndex];
                                }

                                $groupedVariables[$type][] = array(
                                    'name' => $varName,
                                    'type' => $type,
                                    'key' => $match,
                                    'example' => $exampleData,
                                    'context' => ''
                                );
                                $varIndex++;
                            }
                        }
                    }
                }
            }
        }
        foreach ($groupedVariables as $k => $v) {
            if (empty($v))
                unset($groupedVariables[$k]);
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
        $viewer->assign('GROUPED_VARIABLES', $groupedVariables);
        $viewer->assign('COMPONENTS', $components);
        $viewer->assign('MODULES', $modules);
        $viewer->assign('MAPPED_MODULE', $mappedModule);
        $viewer->assign('MAPPINGS', $mappings);
        $viewer->assign('MODULE_FIELDS', $moduleFields);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

        $viewer->view('MappingModal.tpl', $qualifiedModuleName);
    }
}
