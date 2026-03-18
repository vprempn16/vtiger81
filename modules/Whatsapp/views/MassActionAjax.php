<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Whatsapp_MassActionAjax_View extends Vtiger_MassActionAjax_View {

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if ($mode == 'showComposeWhatsappModal') {
            $this->showComposeWhatsappModal($request);
        }
    }

    public function showComposeWhatsappModal(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $sourceModule = $request->get('source_module');
        if(empty($sourceModule)) {
            $sourceModule = $moduleName;
        }
        
        $sourceRecord = $request->get('record');
        
        // 1. Fetch available Phone Fields for the Current Module
        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $phoneFields = $sourceModuleModel->getFieldsByType('phone');
        
        $phoneFieldList = array();
        foreach ($phoneFields as $fieldName => $fieldModel) {
            $phoneFieldList[$fieldName] = vtranslate($fieldModel->get('label'), $sourceModule);
        }

        // 2. Fetch the actual phone numbers for the current record if we have an ID
        $recordPhoneNumbers = array();
        if(!empty($sourceRecord) && !empty($phoneFieldList)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
            foreach($phoneFieldList as $fieldName => $fieldLabel) {
                 $val = $recordModel->get($fieldName);
                 if(!empty($val)) {
                     $recordPhoneNumbers[$fieldName] = $val;
                 }
            }
        }

        // 3. Fetch Active WhatsApp Channels
        $db = PearDatabase::getInstance();
        $channelsResult = $db->pquery("SELECT id, name, phone_number_id FROM vtiger_whatsapp_channels WHERE is_active = 1", array());
        $channels = array();
        while($row = $db->fetch_array($channelsResult)) {
            $channels[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'phone' => $row['phone_number_id']
            );
        }

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('RECORD', $sourceRecord);
        $viewer->assign('PHONE_FIELDS', $phoneFieldList);
        $viewer->assign('RECORD_PHONE_NUMBERS', $recordPhoneNumbers);
        $viewer->assign('CHANNELS', $channels);

        echo $viewer->view('ComposeWhatsappModal.tpl', 'Whatsapp', true);
    }
}
