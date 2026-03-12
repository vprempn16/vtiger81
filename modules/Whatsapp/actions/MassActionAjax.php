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
        
        $db = PearDatabase::getInstance();
        $query = "SELECT id, template_name, language FROM vtiger_whatsapp_templates 
                  WHERE whatsapp_channel_id = ? AND module = ? AND status = 'APPROVED'";
        
        $result = $db->pquery($query, array($channelId, $sourceModule));
        $templates = array();
        
        while($row = $db->fetch_array($result)) {
            $templates[] = array(
                'id' => $row['id'],
                'name' => $row['template_name'],
                'language' => $row['language']
            );
        }

        $response = new Vtiger_Response();
        $response->setResult($templates);
        $response->emit();
    }

    public function getTemplatePreview(Vtiger_Request $request) {
        $templateId = $request->get('template_id');
        $recordId = $request->get('record');
        $sourceModule = $request->get('source_module');

        $db = PearDatabase::getInstance();
        // 1. Get raw template components
        $query = "SELECT components FROM vtiger_whatsapp_templates WHERE id = ?";
        $result = $db->pquery($query, array($templateId));
        
        if($db->num_rows($result) === 0) {
            $response = new Vtiger_Response();
            $response->setError(500, 'Template not found');
            $response->emit();
            return;
        }

        $componentsJson = $db->query_result($result, 0, 'components');
        $components = json_decode($componentsJson, true);

        // 2. We need to parse this template using the record's values.
        // The mappings are in `vtiger_whatsapp_template_map`
        $mapQuery = "SELECT component_type, template_variable, crm_field 
                     FROM vtiger_whatsapp_template_map 
                     WHERE template_id = ?";
        $mapResult = $db->pquery($mapQuery, array($templateId));
        
        $mappings = array();
        while($row = $db->fetch_array($mapResult)) {
            $key = $row['component_type'];
            $mappings[$key][$row['template_variable']] = $row['crm_field'];
        }

        $recordModel = false;
        if(!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
        }

        $previewHtml = "";

        // Iterate standard components: HEADER, BODY, FOOTER, BUTTONS
        foreach($components as $comp) {
            $type = strtoupper($comp['type']);
            
            if($type === 'HEADER') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                // Example: We need to replace {{1}} with the mapped value
                $text = $this->replaceVariables($text, 'HEADER', $mappings, $recordModel);
                if($comp['format'] === 'IMAGE' || $comp['format'] === 'DOCUMENT' || $comp['format'] === 'VIDEO') {
                    $previewHtml .= "<div><i class='fa fa-paperclip'></i> [Media Header: {$comp['format']}]</div>";
                }
                if(!empty($text)) {
                    $previewHtml .= "<strong>{$text}</strong><br><br>";
                }
            } 
            else if($type === 'BODY') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                $text = $this->replaceVariables($text, 'BODY', $mappings, $recordModel);
                $previewHtml .= "<div>" . nl2br(htmlspecialchars($text)) . "</div><br>";
            }
            else if($type === 'FOOTER') {
                $text = isset($comp['text']) ? $comp['text'] : '';
                $previewHtml .= "<small class='text-muted'>" . htmlspecialchars($text) . "</small><br>";
            }
            else if($type === 'BUTTONS') {
                $previewHtml .= "<div style='margin-top:10px;'>";
                foreach($comp['buttons'] as $index => $btn) {
                    $btnType = $btn['type'];
                    $btnText = $btn['text'];
                    if($btnType == 'URL') {
                       $btnUrl = $this->replaceVariables($btn['url'], 'BUTTONS_' . $index, $mappings, $recordModel);
                       $previewHtml .= "<button class='btn btn-default btn-sm' disabled><i class='fa fa-external-link'></i> {$btnText} <br><small>({$btnUrl})</small></button> ";
                    } else {
                       $previewHtml .= "<button class='btn btn-default btn-sm' disabled>{$btnText}</button> ";
                    }
                }
                $previewHtml .= "</div>";
            }
        }

        $response = new Vtiger_Response();
        $response->setResult(array('preview_html' => $previewHtml));
        $response->emit();
    }

    private function replaceVariables($text, $componentContext, $mappings, $recordModel) {
        if(empty($text) || empty($mappings[$componentContext]) || !$recordModel) {
            return $text;
        }

        $contextMappings = $mappings[$componentContext];

        // Replace positional parameters like {{1}}, {{2}} or named like {{name}}
        return preg_replace_callback('/\{\{([^}]+)\}\}/', function($matches) use ($contextMappings, $recordModel) {
            $varName = trim($matches[1]);
            if(isset($contextMappings[$varName])) {
                $crmField = $contextMappings[$varName];
                $val = $recordModel->get($crmField);
                return !empty($val) ? $val : "[No Value]";
            }
            return $matches[0]; // Return original if no mapping found
        }, $text);
    }

    public function sendWhatsappMessage(Vtiger_Request $request) {
        // Pending further instructions on Send Service Architecture
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true, 'message' => "Message Sent Simulation Successful. Ready for integration."));
        $response->emit();
    }
}
