<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_ServiceCompetency_SaveFormAjax_Action extends  Settings_Vtiger_Basic_Action {

    public function process(Vtiger_Request $request) {
        global $current_user,$adb;
        $response = new Vtiger_Response();
        $data = $request->get('formData');
        parse_str($data,$formData);
        $result = false;
        $message = "Failed";
        $roles = array("Not Started"=>"notstarted","Learner"=>"learner","Implementer"=>"implementer","Reviewer"=>"reviewer","Project Manager"=>"projectmanager");
        foreach($roles as $label => $role){
            $metaValue = $formData[$role];
            $metaKey = $role;
            if($metaValue != ''){
                $this->setValue($metaKey,$metaValue);
                $message = 'Success';
                $result = true;
            }
        }
        $response->setResult(array('success'=>$result,'message'=>$message));
        $response->emit();
    }
    function setValue($metakey,$metavalue ){
        global $current_user,$adb;
        $sql =  $adb->pquery("SELECT * FROM atom_role_pricing where meta_key=? ",array($metakey));
        if($adb->num_rows($sql) > 0){
            $adb->pquery("UPDATE atom_role_pricing SET meta_value = ?  WHERE  meta_key = ?",array($metavalue,$metakey));
        }else{
            $adb->pquery("INSERT INTO atom_role_pricing (meta_key, meta_value) VALUES (?,?)",array($metakey,$metavalue));
        }
        return true;

    }
    
}
