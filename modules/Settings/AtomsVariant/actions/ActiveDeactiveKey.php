<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

//ini_set('display_errors','on'); version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);   // DEBUGGING

class Settings_AtomsVariant_ActiveDeactiveKey_Action extends  Settings_Vtiger_Basic_Action {

    public function process(Vtiger_Request $request) {
        global $current_user,$adb;
        $response = new Vtiger_Response();
        $result = false;
        $message = 'failed';
        $action = $request->get('mode');
        $license_key = $request->get('mode');
        $validator = new Settings_AtomsVariant_LicenseManager_Model();
        $records = $validator->getRecordDetails();
        $license_key = $records['cmtmention_license_key'];
        $license_key = Vtiger_Functions::fromProtectedText($license_key);

        $is_validate = $validator->apiCall($license_key,'validate');
        if($is_validate['iskeyvalid']){
            $api_response = $validator->apiCall($license_key,$action);
            if($api_response){
                $message = $api_response['message'];
                $result = $api_response['status'];
            }
            if($api_response['status']){
                if( $action == 'activate'){
                    $validator->ActivateSettings();
               }else if( $action == 'deactivate' ){
                    $validator->DeactivateSettings();
               }  
            }
        }else{
            $message='Please Enter Valid License key';
            $result = false;
        }
        $response->setResult(array('success'=>$result,'message'=>$message));
        $response->emit();
    }
}
