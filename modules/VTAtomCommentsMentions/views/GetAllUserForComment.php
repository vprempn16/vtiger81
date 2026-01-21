<?php
class VTAtomCommentsMentions_GetAllUserForComment_View extends Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		global $current_user,$adb;
		$response = new Vtiger_Response();
		$result = $adb->pquery("SELECT *  FROM vtiger_users WHERE status =?",array('Active'));
		$return = json_encode(array(),true);
		if($adb->num_rows($result) > 0){
            $success = true;
			for($i=0;$i<$adb->num_rows($result);$i++){
				$user_name = $adb->query_result($result,$i,'user_name');
				$name = $adb->query_result($result,$i,'first_name') .' '.$adb->query_result($result,$i,'last_name');
				$users[$i] = array('username'=>$user_name,'label'=>$name);	
			}
		}
        $validator = new Settings_VTAtomCommentsMentions_LicenseManager_Model();

        $licensekey_records = $validator->getRecordDetails();
        $license_key = $licensekey_records['cmtmention_license_key'];
        $license_key = Vtiger_Functions::fromProtectedText($license_key);
        $maskedKey = substr($license_key, 0, 4) . str_repeat('*', strlen($license_key) - 8) . substr($license_key, -4);
        $is_validate = $validator->apiCall($license_key,'validate');
        $is_active = $validator->apiCall($license_key,'is_active');
        $licenseview_url = $validator->getLicenseViewUrl();
        if(!$is_validate['iskeyvalid']){
            $users = '';
            $success = false;
        }
        if(!$is_active['iskeyactive']){
             $users = '';
            $success = false;
        }
		$return = json_encode($users,$success);
		$response->setResult($return);
                $response->emit();
	}

}
?>
