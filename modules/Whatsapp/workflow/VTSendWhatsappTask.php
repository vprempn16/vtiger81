<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/Whatsapp/Services/WhatsAppApiService.php';

class VTSendWhatsappTask extends VTTask {
    public $executeImmediately = true;

    public function getFieldNames() {
        return array('whatsapp_channel', 'recepients', 'templateid', 'wa_mapping');
    }

    public function doTask($entity) {
        try {
            $channelId = $this->whatsapp_channel;
            $templateId = $this->templateid;
            $recipientField = $this->recepients;
            $mapping = $this->wa_mapping;

            if (is_string($mapping)) {
                $mapping = json_decode($mapping, true);
            }

            if (empty($channelId) || empty($templateId) || empty($recipientField)) {
                return;
            }

            if (empty($mapping) || !is_array($mapping)) {
                $mapping = array();
            }

            $apiService = new WhatsAppApiService($channelId);
            $sourceModule = $entity->getModuleName();
            $recordId = $entity->getId();

            if (strpos($recordId, 'x') !== false) {
                $parts = explode('x', $recordId);
                $recordId = $parts[1];
            }
            
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
            
            $to = $recordModel->get($recipientField);
            if (empty($to)) return;

            $normalizedTo = $apiService->formatPhoneNumber($to, $recordModel);

            $build = $apiService->buildTemplateComponentsForWorkflow($templateId, $recordModel, $mapping);
            if (!$build['success']) return;

            require_once 'modules/Users/models/Record.php';

            $logData = array(
                'direction' => 'outgoing',
                'crm_module' => $sourceModule,
                'crm_field' => $recipientField,
                'crm_field_value' => $normalizedTo,
                'whatsapp_no' => $normalizedTo,
                'related_module' => $sourceModule,
                'related_id' => $recordId,
                'template_id' => $templateId,
                'assigned_user_id' => Users_Record_Model::getCurrentUserModel()->getId()
            );

            $sendData = array(
                'type' => 'template',
                'to' => $normalizedTo,
                'details' => array(
                    'templateName' => $build['template_name'],
                    'language' => $build['language'],
                    'components' => $build['components']
                ),
                'logData' => $logData
            );

            $apiService->sendWhatsappMessage($sendData);

        } catch (Exception $e) {
            error_log("VTSendWhatsappTask Error: " . $e->getMessage() . "\nStack: " . $e->getTraceAsString());
        }
    }
}
