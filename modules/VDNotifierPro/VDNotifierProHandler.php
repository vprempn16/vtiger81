<?php

/* * *******************************************************************************
 * The content of this file is subject to the VD VDNotifier Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */


class VDNotifierProHandler extends VTEventHandler{
        private static $oldEntity;
	private static $newEntity;
	private static $entityDelta;

	function handleEvent($eventName, $data) {
                global $log, $current_module, $adb, $current_user;
               
                if (!vtlib_isModuleActive('VDNotifierPro')) {
                        return true;
                }
                $moduleName = $data->getModuleName();
                
                $setting = VDNotifierPro_Module_Model::getSetting($moduleName);

                $VDNotifier = new VDNotifierPro_Record_Model();
                
                $item_summary = false;
        if ($setting['status']==0) return;
                if($eventName == 'vtiger.entity.aftersave') {
                    if ($data->isNew()){
                        if ($setting['newentity']==1) {
                            $VDNotifier->action = 'CREATED';
                        }
                    }
                    else {
                        if ($setting['updateentity']==1) {
                            $VDNotifier->action = 'UPDATED';
                        }
                    }
                }
                elseif ($eventName == 'vtiger.entity.beforedelete'){
                    $VDNotifier->action = 'DELETED';
                    $item_summary = true;
                }
                elseif ($eventName == 'vtiger.entity.afterrestore'){
                    $VDNotifier->action = 'RESTORED';
                     $item_summary = true;
                }
                $VDNotifier->modiuserid = $data->get('modifiedby');

                $recordId = $data->getId();
                $crmentity = VDNotifierPro_Record_Model::findCrmid($recordId); 
                $vtEntityDelta = new VTEntityDelta();
                $delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);
                
                
                
                $VDNotifier->modifiedtime = $delta['modifiedtime']['currentValue'];
                
                $recordModel = Vtiger_Record_Model::getInstanceById( $recordId, $moduleName);
                
                
                
                $link = $recordModel->getDetailViewUrl();
                $focus = CRMEntity::getInstance($moduleName);
                
                $VDNotifier->title = $recordModel->getName();;
                $VDNotifier->link =  str_replace('index.php?', '', $link);;
               
                
                $item_summary = false;
                if ($VDNotifier->action == 'DELETED' || $VDNotifier->action == 'RESTORED') {
                    $current = Users_Record_Model::getCurrentUserModel()->getId();
                } else {
                    $current = $data->get('modifiedby');
                }
                $notifUserId = self::chekVDNotifier($crmentity,$setting,$delta,$current);
                if (!count($notifUserId)) return; 
                $VDNotifier->crmid = $recordId = $data->getId();
                $VDNotifier->modulename = $moduleName;
                if (!$item_summary){
                    $arrayDelta = array();
                    $i=0;
                    foreach ($delta as $fieldName=>$field){
                        
                        if ($fieldName != 'modifiedtime' && $fieldName != 'modifiedby'){
                            if (empty($field['oldValue'])) $arrayDelta[$i]['type'] = 'new';
                            elseif (empty($field['currentValue'])) $arrayDelta[$i]['type'] = 'delete';
                            else $arrayDelta[$i]['type'] = 'change';
                            $arrayDelta[$i]['oldValue'] = $field['oldValue'];
                            $arrayDelta[$i]['currentValue'] = $field['currentValue'];
                            $arrayDelta[$i]['fieldName'] = $fieldName;
                            $i++;
                        }
                    }
                   
                    if (count($arrayDelta)){
                        $item_summary = true;
                        $VDNotifier->item_summary = Zend_Json::encode($arrayDelta);
                    }
                    
                }
                if ($item_summary){
                    foreach ($notifUserId as $userId){
                        $VDNotifier->userid = $userId;
                       
                        $VDNotifier->save($adb);
                    }
                }
                return;
            
        }
        function getAction($moduleName, $recordId) {
            $moduleModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
           
            $recordModel = $moduleModel->getRecord();
           
            $link = $recordModel->getDetailViewUrl();
            $label = $recordModel->getName();
           
            $link = str_replace('index.php?', '', $link);
            
            return array('link'=>$link,'title'=>$label);
        }
        function chekVDNotifier($crmentity,$setting,$delta,$curen_user_id){
            $notifUserId = array();
             if ($curen_user_id != $crmentity['smcreatorid'] && $setting['creator']==1){
                 $notifUserId[] = $crmentity['smcreatorid'];
             }
             if ($curen_user_id != $crmentity['smownerid'] && $setting['owner']==1){
                 $notifUserId[] = $crmentity['smownerid'];
             }
             if ($setting['modif'] == 1 && isset($delta['modifiedby'])){
                 if ($delta['modifiedby']['oldValue'] != $crmentity['smcreatorid'] && $delta['modifiedby']['oldValue'] != $crmentity['smownerid']){
                    $notifUserId[] = $delta['modifiedby']['oldValue'];
                 }
             }
             return array_unique($notifUserId);
             
        }
		

}
