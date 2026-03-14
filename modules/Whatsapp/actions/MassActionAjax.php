<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Whatsapp/Services/WhatsAppApiService.php';

class Whatsapp_MassActionAjax_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if ($mode == 'getTemplatesByChannel') {
            $this->getTemplatesByChannel($request);
        } else if ($mode == 'getTemplatePreview') {
            $this->getTemplatePreview($request);
        } else if ($mode == 'sendWhatsappMessage') {
            $this->sendWhatsappMessage($request);
        } else if ($mode == 'getMappingUIForWorkflow') {
            $this->getMappingUIForWorkflow($request);
        } else if ($mode == 'validateRecipient') {
            $this->validateRecipient($request);
        }
    }

    public function getTemplatesByChannel(Vtiger_Request $request) {
        $channelId = $request->get('channel_id');
        $sourceModule = $request->get('source_module');
        
        $apiService = new WhatsAppApiService($channelId);
        $templates = $apiService->getTemplatesByChannel($channelId, $sourceModule);

        $response = new Vtiger_Response();
        $response->setResult($templates);
        $response->emit();
    }

    public function getTemplatePreview(Vtiger_Request $request) {
        $templateId = $request->get('template_id');
        $recordId = $request->get('record');
        $sourceModule = $request->get('source_module');

        // Note: For preview, we don't strictly need the channel instantiated with API tokens,
        // but we pass a dummy '1' or null just to instantiate the service if needed.
        $apiService = new WhatsAppApiService(null);
        $result = $apiService->getTemplatePreview($templateId, $recordId, $sourceModule);
        
        $response = new Vtiger_Response();
        if ($result['success']) {
            $response->setResult(array('preview_html' => $result['preview_html'], 'isValid' => true));
        } else {
            // Still return result so the frontend can display the error HTML nicely in the preview box
            $response->setResult(array('preview_html' => isset($result['preview_html']) ? $result['preview_html'] : $result['message'], 'isValid' => false));
        }
        $response->emit();
    }

    public function sendWhatsappMessage(Vtiger_Request $request) {
        $channelId = $request->get('channel_id');
        $type = $request->get('type');
        $details = $request->get('details');
        if (is_string($details)) $details = json_decode($details, true);
        
        $recipients = $request->get('recipients'); // JSON array of field names
        if (is_string($recipients)) $recipients = json_decode($recipients, true);
        
        $selectedIds = $request->get('selected_ids');
        if (is_string($selectedIds)) $selectedIds = json_decode($selectedIds, true);
        if (empty($selectedIds)) $selectedIds = array($request->get('record'));

        $sourceModule = $request->get('source_module');

        $apiService = new WhatsAppApiService($channelId);
        $check = $apiService->validateAccount();
        if (!$check['success']) {
            $response = new Vtiger_Response();
            $response->setError(1, $check['message']);
            $response->emit();
            return;
        }

        // Handle Media Upload if present
        if (!empty($_FILES['whatsapp_media']['name'])) {
            $mediaFile = $_FILES['whatsapp_media'];
            
            // Validate MIME Type
            if (!WhatsAppApiService::isMimeTypeSupported($mediaFile['type'])) {
                $response = new Vtiger_Response();
                $supported = WhatsAppApiService::getSupportedExtensions();
                $response->setError(1, "Unsupported file type: {$mediaFile['type']}. Supported types: {$supported}");
                $response->emit();
                return;
            }

            $uploadResult = $apiService->uploadMediaToWhatsApp($mediaFile['tmp_name'], $mediaFile['type'], $mediaFile['name']);
            if ($uploadResult['success']) {
                $type = 'media';
                $details['media_id'] = $uploadResult['media_id'];
                $details['media_record_id'] = null; // We might want to link this to a Vtiger Document later
                $details['media_type'] = explode('/', $mediaFile['type'])[0]; 
                if (!empty($details['text'])) {
                    $details['caption'] = $details['text'];
                }
            } else {
                $response = new Vtiger_Response();
                $response->setError(1, "Media Upload Failed: " . $uploadResult['message']);
                $response->emit();
                return;
            }
        }

        $results = array();
        foreach ($selectedIds as $recordId) {
            if (empty($recordId)) continue;
            try {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
                foreach ($recipients as $phoneField) {
                    $to = $recordModel->get($phoneField);
                    if (empty($to)) {
                        $results[] = array('record' => $recordId, 'field' => $phoneField, 'success' => false, 'error' => 'Phone number empty');
                        continue;
                    }

                    // Normalize NO earlier so it's used for both Log and Send
                    $normalizedTo = $apiService->formatPhoneNumber($to, $recordModel);

                    $logData = array(
                        'direction' => 'outgoing',
                        'crm_module' => $sourceModule,
                        'crm_field' => $phoneField,
                        'crm_field_value' => $normalizedTo,
                        'whatsapp_no' => $normalizedTo,
                        'related_module' => $sourceModule,
                        'related_id' => $recordId,
                        'assigned_user_id' => Users_Record_Model::getCurrentUserModel()->getId()
                    );

                    if ($type === 'template') {
                        $templateId = $details['template_id'];
                        $logData['template_id'] = $templateId;
                        $validate = $apiService->validateTemplateMappings($templateId, $sourceModule);
                        if (!$validate['success']) {
                            $results[] = array('record' => $recordId, 'field' => $phoneField, 'success' => false, 'error' => $validate['message']);
                            continue;
                        }
                        $build = $apiService->buildTemplateComponents($templateId, $recordId, $sourceModule);
                        if (!$build['success']) {
                            $results[] = array('record' => $recordId, 'field' => $phoneField, 'success' => false, 'error' => $build['message']);
                            continue;
                        }
                        $details['templateName'] = $build['template_name'];
                        $details['components'] = $build['components'];
                        $details['language'] = $build['language'];
                    }

                    $sendData = array(
                        'type' => $type,
                        'to' => $normalizedTo,
                        'details' => $details, // This now contains updated template info too
                        'logData' => $logData  // This now contains template_id
                    );

                    $res = $apiService->sendWhatsappMessage($sendData);
                    $results[] = array(
                        'record' => $recordId,
                        'field' => $phoneField,
                        'success' => $res['success'],
                        'message' => $res['success'] ? 'Sent' : $res['message']
                    );
                }
            } catch (Exception $e) {
                $results[] = array('record' => $recordId, 'success' => false, 'error' => $e->getMessage());
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($results);
        $response->emit();
    }

    public function getMappingUIForWorkflow(Vtiger_Request $request) {
        $templateId = $request->get('template_id');
        $sourceModule = $request->get('source_module');

        $apiService = new WhatsAppApiService(null);
        $db = PearDatabase::getInstance();

        // 1. Get raw template components
        $query = "SELECT components FROM vtiger_whatsapp_templates WHERE id = ?";
        $result = $db->pquery($query, array($templateId));
        $componentsStr = html_entity_decode($db->query_result($result, 0, 'components'), ENT_QUOTES, 'UTF-8');
        $components = json_decode($componentsStr, true);

        // 2. Get Module Fields
        $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $fields = $moduleModel->getFields();
        $moduleFields = array();
        foreach ($fields as $fieldName => $fieldModel) {
            $moduleFields[] = array('name' => $fieldName, 'label' => vtranslate($fieldModel->get('label'), $sourceModule));
        }

        // 3. Prepare placeholders with examples
        $placeholders = array('HEADER' => array(), 'BODY' => array(), 'BUTTONS' => array());
        foreach ($components as $comp) {
            $type = strtoupper($comp['type']);
            if ($type === 'HEADER' && ($comp['format'] ?? '') === 'TEXT' && isset($comp['text'])) {
                preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $comp['text'], $matches);
                if (!empty($matches[1])) {
                    $uniqueVars = array_unique($matches[1]);
                    $exampleValues = array();
                    $namedExamples = array();

                    if (isset($comp['example']['header_text'][0])) {
                        $exampleValues = $comp['example']['header_text'];
                    } elseif (isset($comp['example']['header_text_named_params'])) {
                        foreach ($comp['example']['header_text_named_params'] as $np) {
                            $namedExamples[$np['param_name']] = $np['example'];
                        }
                    }

                    $varIndex = 0;
                    foreach ($uniqueVars as $var) {
                        $exVal = '';
                        if (isset($namedExamples[$var])) {
                            $exVal = $namedExamples[$var];
                        } elseif (isset($exampleValues[$varIndex])) {
                            $exVal = $exampleValues[$varIndex];
                        }
                        
                        if (empty($exVal)) $exVal = $var;
                        if (is_array($exVal)) $exVal = reset($exVal);
                        
                        $placeholders['HEADER'][] = array('var' => $var, 'example' => $exVal);
                        $varIndex++;
                    }
                }
            } else if ($type === 'BODY' && isset($comp['text'])) {
                preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $comp['text'], $matches);
                if (!empty($matches[1])) {
                    $uniqueVars = array_unique($matches[1]);
                    $exampleValues = array();
                    $namedExamples = array();

                    if (isset($comp['example']['body_text'][0])) {
                        $exampleValues = $comp['example']['body_text'][0];
                    } elseif (isset($comp['example']['body_text_named_params'])) {
                        foreach ($comp['example']['body_text_named_params'] as $np) {
                            $namedExamples[$np['param_name']] = $np['example'];
                        }
                    }

                    $varIndex = 0;
                    foreach ($uniqueVars as $var) {
                        $exVal = '';
                        if (isset($namedExamples[$var])) {
                            $exVal = $namedExamples[$var];
                        } elseif (isset($exampleValues[$varIndex])) {
                            $exVal = $exampleValues[$varIndex];
                        }
                        
                        if (empty($exVal)) $exVal = $var;
                        if (is_array($exVal)) $exVal = reset($exVal);

                        $placeholders['BODY'][] = array('var' => $var, 'example' => $exVal);
                        $varIndex++;
                    }
                }
            } else if ($type === 'BUTTONS') {
                foreach ($comp['buttons'] as $index => $btn) {
                    if (($btn['type'] ?? '') === 'URL' && !empty($btn['url'])) {
                        preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $btn['url'], $matches);
                        if (!empty($matches[1])) {
                            $uniqueVars = array_unique($matches[1]);
                            $examples = $btn['example'] ?? array();
                            if (isset($examples[0]) && is_array($examples[0])) $examples = $examples[0];
                            
                            $btnVars = array();
                            $varIndex = 0;
                            foreach ($uniqueVars as $var) {
                                $exVal = $examples[$varIndex] ?? $var;
                                if (is_array($exVal)) $exVal = reset($exVal);
                                $btnVars[] = array('var' => $var, 'example' => $exVal);
                                $varIndex++;
                            }
                            
                            $btnNum = $index + 1;
                            $placeholders['BUTTONS'][$btnNum] = array(
                                'label' => $btn['text'] ?? "Button {$btnNum}",
                                'vars' => $btnVars
                            );
                        }
                    }
                }
            }
        }

        // 4. Get Review Preview
        $preview = $apiService->getTemplatePreview($templateId, null, $sourceModule);

        $viewer = Vtiger_Viewer::getInstance();
        $viewer->assign('PLACEHOLDERS', $placeholders);
        $viewer->assign('MODULE_FIELDS', $moduleFields);
        $viewer->assign('PREVIEW_HTML', $preview['preview_html']);
        $viewer->assign('TEMPLATE_ID', $templateId);
        
        $html = $viewer->view('taskforms/VTWhatsappMapping.tpl', 'Whatsapp', true);
        
        $response = new Vtiger_Response();
        $response->setResult($html);
        $response->emit();
    }

    public function validateRecipient(Vtiger_Request $request) {
        global $adb;
        $logFile = 'storage/wa_validation.log';
        $recordId = $request->get('record');
        $sourceModule = $request->get('source_module');
        $phoneField = $request->get('phone_field');

        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Validating with global adb: Record=$recordId, Module=$sourceModule, Field=$phoneField\n", FILE_APPEND);

        $response = new Vtiger_Response();
        try {
            if (empty($recordId) || empty($sourceModule) || empty($phoneField)) {
                throw new Exception("Missing parameters: Record=$recordId, Module=$sourceModule, Field=$phoneField");
            }

            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
            $phoneNumber = (string)$recordModel->get($phoneField);
            
            file_put_contents($logFile, "  - Phone Number: " . $phoneNumber . "\n", FILE_APPEND);

            $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
            $hasCountryCode = (strlen($cleaned) > 10);
            
            // Check if number was ever successfully messaged
            $isExisting = false;
            $query = "SELECT count(*) as count FROM vtiger_whatsapp 
                      INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_whatsapp.whatsappid
                      WHERE vtiger_crmentity.deleted = 0 AND whatsapp_no = ? 
                      AND whatsapp_status IN ('sent', 'delivered', 'read')";
            
            $result = $adb->pquery($query, array($cleaned));
            
            if ($result && $adb->num_rows($result) > 0) {
                $count = $adb->query_result($result, 0, 'count');
                $isExisting = ($count > 0);
                file_put_contents($logFile, "  - Query Result: Found $count records.\n", FILE_APPEND);
            } else {
                file_put_contents($logFile, "  - Query failed or returned no rows.\n", FILE_APPEND);
            }
            
            $resultArr = array(
                'has_country_code' => $hasCountryCode,
                'is_existing' => $isExisting,
                'phone_number' => $phoneNumber
            );
            file_put_contents($logFile, "  - Final Result: " . json_encode($resultArr) . "\n", FILE_APPEND);
            
            $response->setResult($resultArr);
        } catch (Exception $e) {
            file_put_contents($logFile, "  - ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            $response->setError(1, $e->getMessage());
        }
        $response->emit();
    }
}
