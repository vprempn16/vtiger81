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

class Whatsapp_WebhookHandler_Model extends Vtiger_Base_Model
{
    public function handle($payload)
    {
        if (empty($payload['entry'])) return;

        foreach ($payload['entry'] as $entry) {
            if (empty($entry['changes'])) continue;

            foreach ($entry['changes'] as $change) {
                $value = $change['value'] ?? array();
                if (empty($value)) continue;

                // 1. Process Messages (Incoming)
                if (!empty($value['messages'])) {
                    $this->processMessages($value);
                }

                // 2. Process Statuses (Delivery Receipts)
                if (!empty($value['statuses'])) {
                    $this->processStatuses($value);
                }
            }
        }
    }

    protected function processMessages($value)
    {
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
        $channel = WhatsAppApiService::getChannelByPhoneNumberId($phoneNumberId);
        if (!$channel) return;

        $apiService = new WhatsAppApiService($channel);

        foreach ($value['messages'] as $msg) {
            $from = $msg['from'] ?? null;
            $type = $msg['type'] ?? 'text';
            $content = '';
            $mediaId = null;

            if ($type === 'text') {
                $content = $msg['text']['body'] ?? '';
            } elseif (in_array($type, array('image', 'video', 'audio', 'document', 'sticker'))) {
                $content = $msg[$type]['caption'] ?? $type;
                $mediaId = $msg[$type]['id'] ?? null;
            } elseif ($type === 'location') {
                $loc = $msg['location'] ?? array();
                $content = ($loc['name'] ?? '') . ' ' . ($loc['address'] ?? '');
            } elseif ($type === 'button') {
                $content = $msg['button']['text'] ?? 'button_response';
            } elseif ($type === 'interactive') {
                $interactive = $msg['interactive'] ?? array();
                $content = $interactive['list_reply']['title']
                    ?? $interactive['button_reply']['title']
                    ?? 'interactive_response';
            }

            // Find target records to link
            $targets = array();
            $contextId = $msg['context']['id'] ?? null;

            // 1. Try to find parent context for thread continuity
            if ($contextId) {
                $db = PearDatabase::getInstance();
                $parentQuery = "SELECT related_module, related_id, crm_field FROM vtiger_whatsapp 
                                WHERE message_id = ? AND whatsapp_channel_id = ?";
                $pResult = $db->pquery($parentQuery, array($contextId, $channel->getId()));
                
                if ($db->num_rows($pResult)) {
                    $pRow = $db->fetch_array($pResult);
                    if ($pRow['related_id']) {
                        $targets[] = array(
                            'related_module' => $pRow['related_module'],
                            'related_id' => $pRow['related_id'],
                            'crm_field' => $pRow['crm_field']
                        );
                    }
                }
            }

            // 2. If no context or context not found, search by phone number
            if (empty($targets)) {
                $targets = $apiService->findRecordByPhoneNumber($from);
            }

            // 3. Fallback to unlinked message
            if (empty($targets)) {
                $targets[] = array(
                    'related_module' => null,
                    'related_id' => null,
                    'crm_field' => null
                );
            }

            // Create Log for each target
            foreach ($targets as $target) {
                $logData = array(
                    'whatsapp_no' => $from,
                    'direction' => 'incoming',
                    'type' => $type,
                    'message' => $content,
                    'message_id' => $msg['id'] ?? null,
                    'crm_module' => $target['related_module'],
                    'crm_field' => $target['crm_field'],
                    'crm_field_value' => $from,
                    'related_module' => $target['related_module'],
                    'related_id' => $target['related_id'],
                    'media_id' => $mediaId,
                    'info' => $msg
                );
                $apiService->createLog($logData);
            }
        }
    }

    protected function processStatuses($value)
    {
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
        $channel = WhatsAppApiService::getChannelByPhoneNumberId($phoneNumberId);
        if (!$channel) return;

        $db = PearDatabase::getInstance();
        $apiService = new WhatsAppApiService($channel);

        foreach ($value['statuses'] as $status) {
            $messageId = $status['id'] ?? null;
            $newStatus = $status['status'] ?? null; // sent, delivered, read, failed

            if ($messageId && $newStatus) {
                // Find the record in vtiger_whatsapp
                $query = "SELECT whatsappid FROM vtiger_whatsapp WHERE message_id = ? AND whatsapp_channel_id = ?";
                $result = $db->pquery($query, array($messageId, $channel->getId()));

                if ($db->num_rows($result)) {
                    $whatsappId = $db->query_result($result, 0, 'whatsappid');
                    $recordModel = Vtiger_Record_Model::getInstanceById($whatsappId, 'Whatsapp');
                    
                    $response = array(
                        'success' => ($newStatus !== 'failed'),
                        'response' => $status
                    );
                    $apiService->updateLog($recordModel, $response);
                }
            }
        }
    }
}
