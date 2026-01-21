<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_ServiceCompetency_SaveWorkingDays_Action extends  Settings_Vtiger_Basic_Action {

    public function process(Vtiger_Request $request) {
        global $current_user,$adb;
        $response = new Vtiger_Response();
        $data = $request->get('formData');
        parse_str($data,$formData);
        $result = false;
        $message = "Failed";
        $working_days = $request->get('working_days');

        foreach($working_days as $id => $days){
            $days = $days;
            $userid = $id;
            if($days != ''){
                $this->setValue($userid,$days);
                $message = 'Success';
                $result = true;
            }
        }
        $response->setResult(array('success'=>$result,'message'=>$message));
        $response->emit();
    }
    function setValue($userid,$days){
        global $current_user,$adb;
        $sql =  $adb->pquery("SELECT * FROM sc_userworkingdays where userid=? ",array($userid));
        if($adb->num_rows($sql) > 0){
            $adb->pquery("UPDATE sc_userworkingdays SET working_days = ?  WHERE  userid = ?",array($days,$userid));
        }else{
            $adb->pquery("INSERT INTO sc_userworkingdays(userid,working_days) VALUES (?,?)",array($userid,$days));
        }
        return true;
    }
}

