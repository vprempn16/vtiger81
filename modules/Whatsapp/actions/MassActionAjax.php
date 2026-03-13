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
}
