<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_ActionAjax_Action extends Settings_Vtiger_Index_Action
{

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('save');
        $this->exposeMethod('delete');
        $this->exposeMethod('syncTemplates');
        $this->exposeMethod('getModuleFields');
        $this->exposeMethod('saveMapping');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function save(Vtiger_Request $request)
    {
        $qualifiedModuleName = $request->getModule(false);
        $recordId = $request->get('record');
        $recordModel = Settings_Whatsapp_Record_Model::getCleanInstance($qualifiedModuleName);

        if ($recordId) {
            $recordModel->set('id', $recordId);
        }

        $fields = array('name', 'description', 'app_id', 'app_secret', 'phone_number_id', 'business_id', 'access_token', 'is_active');
        $data = array();
        foreach ($fields as $field) {
            $value = $request->get($field);
            $recordModel->set($field, $value);
            $data[$field] = $value;
        }

        // Validate Account before saving
        require_once 'modules/Whatsapp/Services/WhatsAppApiService.php';
        $apiService = new WhatsAppApiService($recordModel);
        $validation = $apiService->validateAccount($data);

        if (!$validation['success']) {
            $response = new Vtiger_Response();
            $response->setError($validation['message']);
            $response->emit();
            return;
        }

        $recordId = $recordModel->save();

        $response = new Vtiger_Response();
        $response->setResult(array('id' => $recordId, 'success' => true));

        // Redirect back to list view after save (non-ajax fallback)
        if (!$request->isAjax()) {
            header("Location: index.php?module=Whatsapp&parent=Settings&view=Settings");
        }
        $response->emit();
    }

    public function delete(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $db = PearDatabase::getInstance();
        $db->pquery('DELETE FROM vtiger_whatsapp_channels WHERE id = ?', array($recordId));
        // Also delete related templates
        $db->pquery('DELETE FROM vtiger_whatsapp_templates WHERE whatsapp_channel_id = ?', array($recordId));

        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
    }

    public function syncTemplates(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $qualifiedModuleName = $request->getModule(false);

        try {
            $recordModel = Settings_Whatsapp_Record_Model::getInstanceById($recordId, $qualifiedModuleName);

            if (!$recordModel) {
                $response = new Vtiger_Response();
                $response->setError('Channel not found for ID: ' . $recordId);
                $response->emit();
                return;
            }

            require_once 'modules/Whatsapp/Services/WhatsAppApiService.php';
            $apiService = new WhatsAppApiService($recordModel);

            // Fetch templates
            $url = $apiService->baseUrl . '/' . $recordModel->get('business_id') . '/message_templates';
            $apiResponse = WhatsAppApiService::request($url, $recordModel->get('access_token'), array(), 'GET');

            if (!$apiResponse['success']) {
                $response = new Vtiger_Response();
                $response->setError('Meta API Error: ' . $apiResponse['message']);
                $response->emit();
                return;
            }

            $templatesData = isset($apiResponse['response']['data']) ? $apiResponse['response']['data'] : array();

            if (!empty($templatesData)) {
                $db = PearDatabase::getInstance();
                foreach ($templatesData as $template) {
                    // Check if exists
                    $check = $db->pquery(
                        'SELECT id FROM vtiger_whatsapp_templates WHERE template_id = ? AND whatsapp_channel_id = ?',
                        array($template['id'], $recordId)
                    );

                    $params = array(
                        $template['name'],
                        $template['status'],
                        $template['category'],
                        $template['language'],
                        json_encode($template['components']),
                        $template['id'],
                        $recordId
                    );

                    if ($db->num_rows($check)) {
                        $query = 'UPDATE vtiger_whatsapp_templates SET template_name=?, status=?, category=?, language=?, components=? WHERE template_id=? AND whatsapp_channel_id=?';
                        $db->pquery($query, $params);
                        $localTemplateId = $db->query_result($check, 0, 'id');
                    } else {
                        $query = 'INSERT INTO vtiger_whatsapp_templates (template_name, status, category, language, components, template_id, whatsapp_channel_id) VALUES (?, ?, ?, ?, ?, ?, ?)';
                        $db->pquery($query, $params);
                        $localTemplateId = $db->getLastInsertID();
                    }

                    // Update Relation Table
                    $relCheck = $db->pquery('SELECT id FROM vtiger_whatsapp_channel_template_rel WHERE whatsapp_channel_id=? AND whatsapp_template_id=?', array($recordId, $template['id']));
                    if ($db->num_rows($relCheck) == 0) {
                        $relId = uniqid('wt_');
                        $db->pquery('INSERT INTO vtiger_whatsapp_channel_template_rel (id, whatsapp_channel_id, whatsapp_template_id) VALUES (?, ?, ?)', array($relId, $recordId, $template['id']));
                    }

                    // Insert template variables into mapping table
                    if (isset($template['components']) && is_array($template['components'])) {
                        foreach ($template['components'] as $component) {
                            $componentBaseType = $component['type'];

                            if ($componentBaseType === 'BUTTONS' && isset($component['buttons'])) {
                                $bIndex = 1;
                                foreach ($component['buttons'] as $btn) {
                                    $componentType = 'BUTTONS_' . $bIndex;
                                    $btnStr = json_encode($btn);
                                    preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $btnStr, $matches);
                                    if (!empty($matches[1])) {
                                        $uniqueVars = array_unique($matches[1]);
                                        foreach ($uniqueVars as $match) {
                                            $variableName = '{{' . $match . '}}';

                                            $mapCheck = $db->pquery(
                                                'SELECT id FROM vtiger_whatsapp_template_map WHERE template_id = ? AND template_variable = ? AND component_type = ?',
                                                array($localTemplateId, $variableName, $componentType)
                                            );

                                            if ($db->num_rows($mapCheck) == 0) {
                                                $db->pquery(
                                                    'INSERT INTO vtiger_whatsapp_template_map (template_id, template_language, template_variable, component_type, crm_module, crm_field) VALUES (?, ?, ?, ?, ?, ?)',
                                                    array($localTemplateId, $template['language'], $variableName, $componentType, null, null)
                                                );
                                            }
                                        }
                                    }
                                    $bIndex++;
                                }
                            } else {
                                $componentType = $componentBaseType;
                                $componentStr = json_encode($component);
                                preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $componentStr, $matches);
                                if (!empty($matches[1])) {
                                    $uniqueVars = array_unique($matches[1]);
                                    foreach ($uniqueVars as $match) {
                                        $variableName = '{{' . $match . '}}';

                                        $mapCheck = $db->pquery(
                                            'SELECT id FROM vtiger_whatsapp_template_map WHERE template_id = ? AND template_variable = ? AND component_type = ?',
                                            array($localTemplateId, $variableName, $componentType)
                                        );

                                        if ($db->num_rows($mapCheck) == 0) {
                                            $db->pquery(
                                                'INSERT INTO vtiger_whatsapp_template_map (template_id, template_language, template_variable, component_type, crm_module, crm_field) VALUES (?, ?, ?, ?, ?, ?)',
                                                array($localTemplateId, $template['language'], $variableName, $componentType, null, null)
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $response = new Vtiger_Response();
            $response->setResult(array('success' => true, 'count' => count($templatesData)));
            $response->emit();

        } catch (Exception $e) {
            $response = new Vtiger_Response();
            $response->setError($e->getMessage());
            $response->emit();
        }
    }

    public function getModuleFields(Vtiger_Request $request)
    {
        $moduleName = $request->get('crm_module');
        $response = new Vtiger_Response();

        if (empty($moduleName)) {
            $response->setResult(array());
            $response->emit();
            return;
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $fields = array();

        if ($moduleModel) {
            $moduleFields = $moduleModel->getFields();
            foreach ($moduleFields as $fieldName => $fieldModel) {
                if ($fieldModel->isViewable()) {
                    $fields[$fieldName] = vtranslate($fieldModel->get('label'), $moduleName);
                }
            }
        }

        $response->setResult($fields);
        $response->emit();
    }

    public function saveMapping(Vtiger_Request $request)
    {
        $localTemplateId = $request->get('template_id');
        $metaTemplateId = $request->get('meta_template_id');
        $templateLanguage = $request->get('template_language');
        $crmModule = $request->get('crm_module');
        $mappings = $request->get('mapping'); // Array of [type][variable] = field

        $db = PearDatabase::getInstance();

        // Update Template's Module
        $db->pquery('UPDATE vtiger_whatsapp_templates SET module = ? WHERE id = ?', array($crmModule, $localTemplateId));

        // Delete old mappings for this template
        $db->pquery('DELETE FROM vtiger_whatsapp_template_map WHERE template_id = ?', array($localTemplateId));

        // Insert new mappings
        if (is_array($mappings)) {
            foreach ($mappings as $componentType => $vars) {
                foreach ($vars as $variableName => $crmField) {
                    $crmFieldToSave = !empty($crmField) ? $crmField : null;
                    $crmModuleToSave = !empty($crmModule) ? $crmModule : null;

                    $db->pquery(
                        'INSERT INTO vtiger_whatsapp_template_map (template_id, template_language, template_variable, component_type, crm_module, crm_field) VALUES (?, ?, ?, ?, ?, ?)',
                        array($localTemplateId, $templateLanguage, $variableName, $componentType, $crmModuleToSave, $crmFieldToSave)
                    );
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult(array('success' => true));
        $response->emit();
    }
}
