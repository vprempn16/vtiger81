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

class Settings_SRColor_checkMetaKeyExists_Action extends  Settings_Vtiger_Basic_Action {

        public function process(Vtiger_Request $request) {
                global $current_user,$adb;
                $response = new Vtiger_Response();
		$selected_module = $request->get('selected_module');
		$module_field = $request->get('module_field');	
		$response->setResult(array('success'=>false));
		if($selected_module != '' && $module_field != ''){
			$meta_key = $selected_module."_".$module_field;
			$flag = false;
			$sql = $adb->pquery("SELECT * FROM sr_color_configuration WHERE meta_key =?",array($meta_key));
			if($adb->num_rows($sql) > 0){
				$id = $adb->query_result($sql,0,'id');
				$flag = true;
			}
		}
		$response->setResult(array('success'=>$flag,'id'=>$id));
		$response->emit();
	}
}

?>
