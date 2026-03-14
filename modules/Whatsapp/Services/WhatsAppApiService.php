<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Settings/Whatsapp/models/Record.php';

class WhatsAppApiService
{
    protected $channel;
    protected $accessToken;
    protected $phoneNumberId;
    protected $businessId;
    public $baseUrl = 'https://graph.facebook.com/v21.0';

    public function findRecordByPhoneNumber($number)
    {
        $db = PearDatabase::getInstance();
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        if (empty($cleaned)) return array();

        // We search in Leads, Contacts, and Accounts
        $queries = array(
            'Leads' => "SELECT leadid as id FROM vtiger_leaddetails 
                        INNER JOIN vtiger_crmobject ON vtiger_crmobject.crmid = vtiger_leaddetails.leadid
                        WHERE vtiger_crmobject.deleted = 0 AND (phone LIKE ? OR mobile LIKE ?)",
            'Contacts' => "SELECT contactid as id FROM vtiger_contactdetails 
                           INNER JOIN vtiger_crmobject ON vtiger_crmobject.crmid = vtiger_contactdetails.contactid
                           WHERE vtiger_crmobject.deleted = 0 AND (phone LIKE ? OR mobile LIKE ?)",
            'Accounts' => "SELECT accountid as id FROM vtiger_account 
                           INNER JOIN vtiger_crmobject ON vtiger_crmobject.crmid = vtiger_account.accountid
                           WHERE vtiger_crmobject.deleted = 0 AND (phone LIKE ? OR otherphone LIKE ?)"
        );

        $results = array();
        $searchPattern = "%$cleaned%";

        foreach ($queries as $module => $sql) {
            $res = $db->pquery($sql, array($searchPattern, $searchPattern));
            while ($row = $db->fetch_array($res)) {
                $results[] = array(
                    'related_module' => $module,
                    'related_id' => $row['id'],
                    'crm_field' => ($module === 'Leads' || $module === 'Contacts') ? 'mobile' : 'phone'
                );
            }
        }
        return $results;
    }

    public static function getChannelByPhoneNumberId($phoneNumberId)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT id FROM vtiger_whatsapp_channels WHERE phone_number_id = ? AND is_active = 1", array($phoneNumberId));
        if ($db->num_rows($result)) {
            $id = $db->query_result($result, 0, 'id');
            return Settings_Whatsapp_Record_Model::getInstanceById($id, 'Settings:Whatsapp');
        }
        return false;
    }

    // Meta-supported MIME types for WhatsApp API
    public static $ALLOWED_MIME_TYPES = array(
        // Audio
        'audio/aac', 'audio/mp4', 'audio/mpeg', 'audio/amr', 'audio/ogg', 'audio/opus',
        // Documents
        'application/vnd.ms-powerpoint', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/pdf', 'text/plain', 'application/vnd.ms-excel',
        // Images
        'image/jpeg', 'image/png', 'image/webp',
        // Video
        'video/mp4', 'video/3gpp'
    );

    public function __construct($channel)
    {
        if (is_numeric($channel)) {
            $this->channel = Settings_Whatsapp_Record_Model::getInstanceById($channel, 'Settings:Whatsapp');
        } else {
            $this->channel = $channel;
        }

        if ($this->channel) {
            $this->accessToken = $this->channel->get('access_token');
            $this->phoneNumberId = $this->channel->get('phone_number_id');
            $this->businessId = $this->channel->get('business_id');
        }
    }

    /**
     * Validate WhatsApp Account credentials
     * @param array $data Input data for validation (optional)
     * @return array
     */
    public function validateAccount(array $data = array())
    {
        $businessId = $this->businessId ?: ($data['business_id'] ?? '');
        $accessToken = $this->accessToken ?: ($data['access_token'] ?? '');

        if (empty($businessId)) {
            return array(
                'success' => false,
                'message' => 'Business Id Required'
            );
        }

        $url = "{$this->baseUrl}/{$businessId}";
        $response = self::request($url, $accessToken, array('fields' => 'id'), 'GET');

        if (($response['success'] ?? false) !== true) {
            return array(
                'success' => false,
                'message' => 'Unable to connect to WhatsApp API: ' . ($response['message'] ?? 'Unknown error')
            );
        }

        if (!empty($response['response']['error'])) {
            return array(
                'success' => false,
                'message' => $response['response']['error']['message'] ?? 'Invalid WhatsApp credentials'
            );
        }

        if (($response['response']['id'] ?? null) !== $businessId) {
            return array(
                'success' => false,
                'message' => 'Business ID mismatch'
            );
        }

        return array('success' => true);
    }

    public function formatPhoneNumber($phoneNumber, $recordModel = null)
    {
        // 1. Clean all symbols (spaces, dashes, parens)
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Return cleaned number (User has new plan for normalization)
        return $cleaned;
    }

    /**
     * Common CURL request function
     */
    public static function request($url, $accessToken, $payload = array(), $method = 'POST', $headers = array(), $isMultipart = false)
    {
        $ch = curl_init();
        $defaultHeaders = array(
            'Authorization: Bearer ' . $accessToken,
        );
        if (!$isMultipart) {
            $defaultHeaders[] = 'Content-Type: application/json';
        }

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
        );

        switch (strtoupper($method)) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = $isMultipart ? $payload : json_encode($payload);
                break;
            case 'PUT':
            case 'PATCH':
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                $options[CURLOPT_POSTFIELDS] = $isMultipart ? $payload : json_encode($payload);
                break;
            case 'GET':
                if (!empty($payload)) {
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($payload);
                }
                break;
            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return array(
                'success' => false,
                'message' => $error,
            );
        }

        $decoded = json_decode($response, true);
        $errorMsg = '';
        $success = ($httpCode >= 200 && $httpCode < 300);

        if (!empty($decoded['error'])) {
            $success = false;
            $errorMsg = $decoded['error']['message'] ?? 'Unknown WhatsApp API error';
        }

        if (!$success && empty($errorMsg)) {
            $errorMsg = "HTTP request failed with status code: {$httpCode}";
        }

        return array(
            'httpCode' => $httpCode,
            'success' => $success,
            'message' => $errorMsg,
            'response' => $decoded,
        );
    }

    /**
     * Fetch templates from Meta Graph API
     * @return array
     */
    public function fetchTemplates()
    {
        if (empty($this->businessId) || empty($this->accessToken)) {
            return array();
        }

        $url = "{$this->baseUrl}/{$this->businessId}/message_templates";
        $response = self::request($url, $this->accessToken, array(), 'GET');

        if ($response['success']) {
            return isset($response['response']['data']) ? $response['response']['data'] : array();
        }

        return array();
    }

    /**
     * Send Message (Placeholder for future implementation)
     */
    public function sendMessage($to, $templateName, $languageCode, $components = array())
    {
        // Implementation for sending messages via Meta API
    }

    // --- Methods moved from MassActionAjax ---

    public function getTemplatesByChannel($channelId, $sourceModule = null)
    {
        $db = PearDatabase::getInstance();
        $params = array($channelId);
        $query = "SELECT id, template_name, language FROM vtiger_whatsapp_templates 
                  WHERE whatsapp_channel_id = ? AND status = 'APPROVED'";
        
        if (!empty($sourceModule)) {
            $query .= " AND module = ?";
            $params[] = $sourceModule;
        }
        $templates = array();
        $result = $db->pquery($query, $params);

        while ($row = $db->fetch_array($result)) {
            $templates[] = array(
                'id' => $row['id'],
                'name' => $row['template_name'],
                'language' => $row['language']
            );
        }
        return $templates;
    }

    public function getTemplatePreview($templateId, $recordId, $sourceModule)
    {
        $db = PearDatabase::getInstance();

        // 1. Get raw template components
        $query = "SELECT components FROM vtiger_whatsapp_templates WHERE id = ?";
        $result = $db->pquery($query, array($templateId));

        if ($db->num_rows($result) === 0) {
            return array('success' => false, 'message' => 'Template not found');
        }

        $componentsJson = $db->query_result($result, 0, 'components');
        $componentsJson = htmlspecialchars_decode($componentsJson, ENT_QUOTES);
        $components = json_decode($componentsJson, true);

        if (!is_array($components)) {
            $components = array();
        }

        // 2. We need to parse this template using the record's values.
        $mapQuery = "SELECT component_type, template_variable, crm_field 
                     FROM vtiger_whatsapp_template_map 
                     WHERE template_id = ?";
        $mapResult = $db->pquery($mapQuery, array($templateId));

        $mappings = array();
        while ($row = $db->fetch_array($mapResult)) {
            $key = $row['component_type'];
            $mappings[$key][$row['template_variable']] = $row['crm_field'];
        }

        $recordModel = false;
        if (!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
        }

        $previewHtml = "";
        $errors = array();

        // Helper function for replacing and validating variables
        $replaceVariables = function ($text, $componentContext) use ($mappings, $recordModel, &$errors) {
            if (empty($text) || empty($mappings[$componentContext]) || !$recordModel) {
                return $text;
            }

            $contextMappings = $mappings[$componentContext];

            // Replace parameters like {{1}}, {{2}} or {{name}}
            return preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($contextMappings, $recordModel, &$errors) {
                $varName = trim($matches[1]);
                $fullVar = '{{' . $varName . '}}'; // Match the DB column format

                // Also check if mapping exists without brackets just in case
                $lookupVar = isset($contextMappings[$fullVar]) ? $fullVar : (isset($contextMappings[$varName]) ? $varName : null);

                if ($lookupVar !== null) {
                    $crmField = $contextMappings[$lookupVar];
                    $val = $recordModel->get($crmField);

                    // VALIDATION CHECK: Value is mandatory
                    if ($val === '' || $val === null) {
                        $moduleModel = $recordModel->getModule();
                        $fieldModel = $moduleModel->getField($crmField);
                        $fieldLabel = $fieldModel ? vtranslate($fieldModel->get('label'), $recordModel->getModuleName()) : $crmField;

                        $errors[] = "Field value is mandatory: '$fieldLabel' has no value.";
                        return "<span style='color:red; font-weight:bold;'>[Missing: $fieldLabel]</span>";
                    }
                    return $val;
                }
                return $matches[0]; // Return original if no mapping found
            }, $text);
        };

        // Iterate standard components: HEADER, BODY, FOOTER, BUTTONS
        foreach ($components as $comp) {
            $type = strtoupper($comp['type']);

            if ($type === 'HEADER') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                $text = $replaceVariables($text, 'HEADER');
                if ($comp['format'] === 'IMAGE' || $comp['format'] === 'DOCUMENT' || $comp['format'] === 'VIDEO') {
                    $previewHtml .= "<div><i class='fa fa-paperclip'></i> [Media Header: {$comp['format']}]</div>";
                }
                if (!empty($text)) {
                    $previewHtml .= "<strong>{$text}</strong><br><br>";
                }
            } else if ($type === 'BODY') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                $text = $replaceVariables($text, 'BODY');
                $previewHtml .= "<div>" . nl2br($text) . "</div><br>"; // Allow HTML spanning for errors
            } else if ($type === 'FOOTER') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                $previewHtml .= "<small class='text-muted'>" . htmlspecialchars($text) . "</small><br>";
            } else if ($type === 'BUTTONS') {
                $previewHtml .= "<div style='margin-top:10px;'>";
                foreach ($comp['buttons'] as $index => $btn) {
                    // $index is 0-based from JSON, mapping uses 1-based index (e.g. BUTTONS_1)
                    $btnType = $btn['type'];
                    $btnText = $btn['text'];
                    if ($btnType == 'URL') {
                        $btnUrl = $replaceVariables($btn['url'], 'BUTTONS_' . ($index + 1));
                        $previewHtml .= "<button class='btn btn-default btn-sm' disabled><i class='fa fa-external-link'></i> {$btnText} <br><small>({$btnUrl})</small></button> ";
                    } else {
                        $previewHtml .= "<button class='btn btn-default btn-sm' disabled>{$btnText}</button> ";
                    }
                }
                $previewHtml .= "</div>";
            }
        }

        if (!empty($errors)) {
            $errorHtml = "<div class='alert alert-danger'><strong>Cannot Send Template:</strong><ul>";
            foreach ($errors as $err) {
                $errorHtml .= "<li>$err</li>";
            }
            $errorHtml .= "</ul>Please update the record with the required information.</div>";
            return array('success' => false, 'message' => 'Missing mapped field values', 'preview_html' => $errorHtml . $previewHtml);
        }

        return array('success' => true, 'preview_html' => $previewHtml);
    }

    public function sendWhatsappMessage($requestData)
    {
        $type = $requestData['type'];
        $to = $requestData['to'];
        $details = $requestData['details'];
        $logData = $requestData['logData'];

        switch ($type) {
            case 'message':
                return $this->sendTextMessage($to, $details['text'], $logData);
            case 'template':
                return $this->sendTemplateMessage($to, $details['templateName'], $details['language'], $details['components'], $logData);
            case 'media':
                return $this->sendMediaMessage($to, $details['media_type'], $details['media_record_id'], $details['media_id'], $details['caption'], $logData);
            default:
                return array('success' => false, 'message' => 'Invalid message type');
        }
    }

    public function createLog($data)
    {
        try {
            $recordModel = Vtiger_Record_Model::getCleanInstance('Whatsapp');
            $recordModel->set('whatsapp_channel_id', $this->channel ? $this->channel->getId() : null);
            $recordModel->set('whatsapp_no', $data['whatsapp_no'] ?? null);
            $recordModel->set('direction', $data['direction'] ?? 'outgoing');
            $recordModel->set('type', $data['type']);
            $recordModel->set('message', $data['message'] ?? null);
            $recordModel->set('crm_module', $data['crm_module'] ?? null);
            $recordModel->set('crm_field', $data['crm_field'] ?? null);
            $recordModel->set('crm_field_value', $data['crm_field_value'] ?? null);
            $recordModel->set('related_module', $data['related_module'] ?? null);
            $recordModel->set('related_id', $data['related_id'] ?? null);
            $recordModel->set('media_id', $data['media_id'] ?? null);
            $recordModel->set('whatsapp_status', 'open');
            $recordModel->set('whatsapp_info', isset($data['info']) ? json_encode($data['info'], JSON_UNESCAPED_UNICODE) : null);
            $recordModel->set('assigned_user_id', $data['assigned_user_id'] ?? Users_Record_Model::getCurrentUserModel()->getId());
            $recordModel->save();
            return $recordModel;
        } catch (Exception $e) {
            error_log("WhatsApp createLog Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateLog($recordModel, $response)
    {
        if (!$recordModel) {
            return;
        }
        try {
            $info = array();
            $existingInfo = $recordModel->get('whatsapp_info');
            if ($existingInfo) {
                $info = json_decode($existingInfo, true) ?: array();
            }

            if ($response['success']) {
                $status = 'sent';
                $messageId = $response['response']['messages'][0]['id'] ?? null;
                $info['status'] = 'sent';
                $info['response'] = $response;
                $recordModel->set('message_id', $messageId);
            } else {
                $status = 'failed';
                $info['status'] = 'failed';
                $info['error'] = $response;
            }
            
            $recordModel->set('whatsapp_status', $status);
            $recordModel->set('whatsapp_info', json_encode($info, JSON_UNESCAPED_UNICODE));
            $recordModel->set('id', $recordModel->getId());
            $recordModel->set('mode', 'edit');
            $recordModel->save();
        } catch (Exception $e) {
            error_log("WhatsApp updateLog Error: " . $e->getMessage());
        }
    }

    public function sendTextMessage($to, $message, $logData)
    {
        $logData['type'] = 'message';
        $logData['message'] = $message;
        $log = $this->createLog($logData);

        $payload = array(
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => array('body' => $message)
        );

        $response = self::request("{$this->baseUrl}/{$this->phoneNumberId}/messages", $this->accessToken, $payload);
        if (!$response['success'] && !empty($response['response']['error']['message'])) {
            $response['message'] = $response['response']['error']['message'];
        }
        $this->updateLog($log, $response);
        return $response;
    }

    public function sendTemplateMessage($to, $templateName, $language, $components, $logData)
    {
        $logData['type'] = 'template';

        // Construct plain text message for the log
        $templateId = $logData['template_id'] ?? null;
        if ($templateId) {
            $buildFull = $this->getTemplatePreview($templateId, $logData['related_id'], $logData['crm_module']);
            if ($buildFull['success']) {
                $logData['message'] = strip_tags(str_replace('<br>', "\n", $buildFull['preview_html']));
            } else {
                $logData['message'] = "Template: $templateName (Preview Failed: " . ($buildFull['message'] ?? 'Unknown Error') . ")";
            }
        } else {
            $logData['message'] = "Template: $templateName";
        }

        $log = $this->createLog($logData);

        $payload = array(
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => array(
                'name' => $templateName,
                'language' => array('code' => $language),
                'components' => $components
            )
        );

        $response = self::request("{$this->baseUrl}/{$this->phoneNumberId}/messages", $this->accessToken, $payload);
        
        // DEBUG LOGGING
        file_put_contents('/tmp/wa_payload.log', "\n---\n" . date('Y-m-d H:i:s') . "\n" . json_encode([
            'to' => $to,
            'template' => $templateName,
            'payload' => $payload,
            'response' => $response
        ], JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

        if (!$response['success'] && !empty($response['response']['error']['message'])) {
            $response['message'] = $response['response']['error']['message'];
        }
        $this->updateLog($log, $response);
        return $response;
    }

    public function sendMediaMessage($to, $type, $mediaRecordId, $mediaId, $caption, $logData)
    {
        $logData['type'] = 'media';
        // Use local record ID if available, otherwise fallback to Meta Media ID for logging
        $logData['media_id'] = !empty($mediaRecordId) ? $mediaRecordId : $mediaId;
        $logData['message'] = $caption;
        $log = $this->createLog($logData);

        $payload = array(
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => $type,
            $type => array(
                'id' => $mediaId
            )
        );
        if ($caption && in_array($type, array('image', 'video', 'document'))) {
            $payload[$type]['caption'] = $caption;
        }

        $response = self::request("{$this->baseUrl}/{$this->phoneNumberId}/messages", $this->accessToken, $payload);
        $this->updateLog($log, $response);
        return $response;
    }

    public function sendRawMessage($to, $payload)
    {
        $fullPayload = array_merge(array(
            'messaging_product' => 'whatsapp',
            'to' => $to
        ), $payload);
        return self::request("{$this->baseUrl}/{$this->phoneNumberId}/messages", $this->accessToken, $fullPayload);
    }

    public function uploadMediaToWhatsApp($filePath, $mimeType, $fileName)
    {
        // 1. Move file to permanent storage
        $storageDir = 'storage/whatsapp/';
        if (!is_dir($storageDir)) {
            if (!mkdir($storageDir, 0777, true)) {
                return array('success' => false, 'message' => 'Failed to create WhatsApp storage directory');
            }
        }
        
        $uniqueName = time() . '_' . $fileName;
        $localPath = $storageDir . $uniqueName;
        
        if (!move_uploaded_file($filePath, $localPath)) {
            // If it's not an uploaded file (e.g. from local server path), try copy
            if (!copy($filePath, $localPath)) {
                return array('success' => false, 'message' => 'Failed to save file to local storage');
            }
        }

        $url = "{$this->baseUrl}/{$this->phoneNumberId}/media";
        $payload = array(
            'messaging_product' => 'whatsapp',
            'type' => $mimeType,
            'file' => new CURLFile($localPath, $mimeType, $fileName)
        );

        $response = self::request($url, $this->accessToken, $payload, 'POST', array(), true);

        if ($response['success'] && !empty($response['response']['id'])) {
            $mediaId = $response['response']['id'];
            
            // Log to vtiger_whatsapp_media for tracking
            $db = PearDatabase::getInstance();
            $mediaTableId = $db->getUniqueID('vtiger_whatsapp_media');
            $db->pquery("INSERT INTO vtiger_whatsapp_media (id, whatsapp_channel_id, media_id, mime_type, file_name, local_path, created_by) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)", 
                         array($mediaTableId, $this->channel ? $this->channel->getId() : null, $mediaId, $mimeType, $fileName, $localPath, Users_Record_Model::getCurrentUserModel()->getId()));

            return array('success' => true, 'media_id' => $mediaId);
        }
        return array('success' => false, 'message' => $response['message'] ?? 'Media upload failed');
    }

    private function getMimeType($filePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        return $mimeType;
    }

    public static function isMimeTypeSupported($mimeType)
    {
        return in_array(strtolower($mimeType), self::$ALLOWED_MIME_TYPES);
    }

    public static function getSupportedExtensions()
    {
        return '.jpg, .jpeg, .png, .webp, .pdf, .txt, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .mp4, .3gp, .aac, .mpeg, .amr, .ogg, .opus';
    }

    public function validateTemplateMappings($templateId, $sourceModule)
    {
        $db = PearDatabase::getInstance();
        $query = "SELECT components FROM vtiger_whatsapp_templates WHERE id = ?";
        $result = $db->pquery($query, array($templateId));
        if ($db->num_rows($result) === 0)
            return array('success' => false, 'message' => 'Template not found');

        $components = json_decode(htmlspecialchars_decode($db->query_result($result, 0, 'components'), ENT_QUOTES), true);

        $requiredVars = array();
        foreach ($components as $component) {
            if (!empty($component['text'])) {
                preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $component['text'], $matches);
                foreach ($matches[0] as $var)
                    $requiredVars[] = $var;
            }
            if (($component['type'] ?? '') === 'BUTTONS') {
                foreach ($component['buttons'] ?? array() as $btn) {
                    if (($btn['type'] ?? '') === 'URL' && !empty($btn['url'])) {
                        preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $btn['url'], $matches);
                        foreach ($matches[0] as $var)
                            $requiredVars[] = $var;
                    }
                }
            }
        }
        $requiredVars = array_unique($requiredVars);
        if (empty($requiredVars))
            return array('success' => true);

        $query = "SELECT template_variable FROM vtiger_whatsapp_template_map WHERE template_id = ? AND crm_field != '' AND crm_field IS NOT NULL";
        $result = $db->pquery($query, array($templateId));
        $mappedVars = array();
        while ($row = $db->fetch_array($result))
            $mappedVars[] = $row['template_variable'];

        $missing = array_diff($requiredVars, $mappedVars);

        if (!empty($missing))
            return array('success' => false, 'message' => 'Missing mappings', 'missing_variables' => array_values($missing));

        return array('success' => true);
    }

    public function buildTemplateComponents($templateId, $recordId, $sourceModule)
    {
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
        
        $db = PearDatabase::getInstance();
        $query = "SELECT component_type, template_variable, crm_field FROM vtiger_whatsapp_template_map WHERE template_id = ?";
        $result = $db->pquery($query, array($templateId));
        $valueMap = array();
        while ($row = $db->fetch_array($result)) {
            $val = $recordModel->get($row['crm_field']);
            $valueMap[$row['component_type']][$row['template_variable']] = (string) $val;
        }

        return $this->buildTemplateComponentsWithMapping($templateId, $recordModel, $valueMap);
    }

    public function buildTemplateComponentsForWorkflow($templateId, $recordModel, $mapping)
    {
        $valueMap = array();
        foreach ($mapping as $componentType => $vars) {
            foreach ($vars as $varName => $crmField) {
                $val = $recordModel->get($crmField);
                $valueMap[$componentType][$varName] = (string) $val;
            }
        }
        return $this->buildTemplateComponentsWithMapping($templateId, $recordModel, $valueMap);
    }

    private function buildTemplateComponentsWithMapping($templateId, $recordModel, $valueMap)
    {
        $db = PearDatabase::getInstance();
        $query = "SELECT template_name, language, components, format FROM vtiger_whatsapp_templates WHERE id = ?";
        $result = $db->pquery($query, array($templateId));
        if ($db->num_rows($result) === 0)
            return array('success' => false, 'message' => 'Template not found');

        $row = $db->fetch_array($result);
        $templateName = $row['template_name'];
        $language = $row['language'];
        $format = $row['format'];
        $components = json_decode(htmlspecialchars_decode($row['components'], ENT_QUOTES), true);

        $builtComponents = array();
        foreach ($components as $component) {
            $type = strtoupper($component['type']);
            if ($type === 'HEADER' && ($component['format'] ?? '') === 'TEXT') {
                if (strpos($component['text'], '{{') !== false) {
                    $builtComponents[] = array(
                        'type' => 'header',
                        'parameters' => $this->buildParams($component['text'], $valueMap['HEADER'] ?? array(), $format === 'NAMED')
                    );
                }
            } elseif ($type === 'BODY') {
                $builtComponents[] = array(
                    'type' => 'body',
                    'parameters' => $this->buildParams($component['text'], $valueMap['BODY'] ?? array(), $format === 'NAMED')
                );
            } elseif ($type === 'BUTTONS') {
                foreach ($component['buttons'] as $index => $button) {
                    if ($button['type'] === 'URL' && strpos($button['url'], '{{') !== false) {
                        $builtComponents[] = array(
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => (string) $index,
                            'parameters' => $this->buildParams($button['url'], $valueMap['BUTTONS_' . ($index + 1)] ?? array(), $format === 'NAMED')
                        );
                    }
                }
            }
        }

        return array('success' => true, 'template_name' => $templateName, 'language' => $language, 'components' => $builtComponents);
    }

    private function buildParams($text, $values, $isNamed)
    {
        $params = array();
        // Regex to find all {{variable}} patterns
        if (preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $text, $matches)) {
            foreach ($matches[1] as $index => $varName) {
                $fullMatch = $matches[0][$index]; // {{name}}
                
                // Try with braces first, then without
                $val = isset($values[$fullMatch]) ? $values[$fullMatch] : (isset($values[$varName]) ? $values[$varName] : '');

                $param = array('type' => 'text', 'text' => (string) $val);

                // Meta NAMED templates require parameter_name. 
                // We use isNamed flag OR check if the variable itself is non-numeric.
                if ($isNamed || !is_numeric($varName)) {
                    $param['parameter_name'] = $varName;
                }

                $params[] = $param;
            }
        }
        return $params;
    }

}
