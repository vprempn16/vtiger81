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

class Settings_AtomsVariant_SaveLicense_Action extends  Settings_Vtiger_Basic_Action {
    public function process(Vtiger_Request $request) {
        global $current_user,$adb;
        $response = new Vtiger_Response();
        $data = $request->get('formData');
        parse_str($data,$formData);

        $license_key = $formData['cmtmention_license_key'];
        $result = false;
        $message = "Failed";
        $validator = new Settings_AtomsVariant_LicenseManager_Model();

        $action = 'validate';
        if($license_key == ''){
            $license_key = $this->getValue('cmtmention_license_key');
        }
        if($license_key == ''){
            $message = 'Please Enter License key';
            $result = false;
        }
        if($license_key != ''){
            $license_key = Vtiger_Functions::fromProtectedText($license_key);

            $checkApiKey = $validator->apiCall($license_key,$action);
            if(!$checkApiKey['status']){
                $message = 'Please Enter Valid License key';
                $result = false;
            }
            if($checkApiKey['status']){
                if($formData['cmtmention_license_key'] == ''){
                    $formData['cmtmention_license_key'] = $license_key;
                }
                $formData['cmtmention_license_key'] = Vtiger_Functions::toProtectedText($formData['cmtmention_license_key']);
                foreach($formData as $key => $value){
                    if($key == 'cmtmention_license_key'){
                        $this->setVelue($key,$value);
                    }
                }
                $message = 'Success';
                $result = true; 
            }
        }
        $response->setResult(array('success'=>$result,'message'=>$message));
        $response->emit();
    

    }
    function getValue($metakey){
        global $current_user,$adb;
        $value = false;
                $sql = $adb->pquery("SELECT * FROM atom_license_manager where meta_key=?",array($metakey));
        if($adb->num_rows($sql) > 0){
            $value = $adb->query_result($sql,0,'meta_value');
        }
        return $value;
    }
    function setVelue($metakey,$metavalue){
        global $current_user,$adb;
        $sql =  $adb->pquery("SELECT * FROM atom_license_manager where meta_key=? ",array($metakey));
        if($adb->num_rows($sql) > 0){
            $adb->pquery("UPDATE atom_license_manager SET meta_value = ?  WHERE  meta_key = ?",array($metavalue,$metakey));
        }else{
            $adb->pquery("INSERT INTO atom_license_manager (meta_key, meta_value) VALUES (?,?)",array($metakey,$metavalue));
        }
        return true;
    }

}
