<?php 

class Settings_ServiceCompetency_Record_Model extends  Vtiger_Base_Model {

    public function getMenuItem() {
        $menuItem = Settings_Vtiger_MenuItem_Model::getInstance('Role Pricing');
        return $menuItem;
    }
    public function getRecordDetails(){
        global $adb;
        $retun = Array();
        $result = $adb->pquery("SELECT * FROM atom_role_pricing",array( ));
        $num_rows = $adb->num_rows($result);
        if($num_rows > 0){
            for($i=0;$i<$num_rows;$i++){
                $options = $adb->query_result($result,$i,'meta_value');
                $key = $adb->query_result($result,$i,'meta_key');
                $return[$key] = $options;
            }
        }
        return $return;

    } 
    public function getAllUsers(){
        global $adb;
        $retun = Array();
                $result = $adb->pquery("SELECT *  FROM vtiger_users WHERE status =?",array('Active'));
        $num_rows = $adb->num_rows($result);
        if($num_rows > 0){
            for($i=0;$i<$num_rows;$i++){
                $userid = $adb->query_result($result,$i,'id');
                $first_name = $adb->query_result($result,$i,'first_name') .' '.$adb->query_result($result,$i,'last_name');
                $return[$userid] = $first_name;
            }
        }
        return $return;
    }
    public function getUserWorkingDays(){
        global $adb;
        $retun = Array();
        $result = $adb->pquery("SELECT * FROM sc_userworkingdays",array( ));
        $num_rows = $adb->num_rows($result);
        if($num_rows > 0){
            for($i=0;$i<$num_rows;$i++){
                $userid = $adb->query_result($result,$i,'userid');
		$working_days = $adb->query_result($result,$i,'working_days');
		$working_hours = $adb->query_result($result,$i,'working_hours');
                $return[$userid]['working_days'] = $working_days;
		$return[$userid]['working_hours'] = $working_hours;
            }
        }
        return $return;
    }
}
