
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

class Settings_SRColor_SaveFormAjax_Action extends  Settings_Vtiger_Basic_Action {

        public function process(Vtiger_Request $request) {
                global $current_user,$adb;
		$response = new Vtiger_Response();
		$name = $request->get('name');
		$selected_module = $request->get('selected_module');
		$field = $request->get('field');
		$recordModel = new Settings_SRColor_Record_Model();
		$listview_URL = $recordModel->getListViewUrl();
		$meta_key = $selected_module."_".$field;
		$status = $request->get('status');
		$check = 0;
		if($status == 'on'){
			$check = 1;
		}
		/*foreach ($_POST as $key => $value) {
			if (strpos($key, '_') !== false) {
				$newKey = str_replace('_', ' ', $key);
				$_POST[$newKey] = $value;
				unset($_POST[$key]);
			}
		}*/
		//echo"<pre>";print_r([$_POST]);die('s');
		$meta_value = base64_encode(serialize($_POST));
		$response->setResult(array('success'=>false,'message'=>'failed'));
		$id = $request->get('id');
		if($id != ''){
			$adb->pquery("UPDATE sr_color_configuration SET name=?,status=?,meta_key=?,meta_value =?  WHERE id = ?",array($name,$check,$meta_key,$meta_value,$id));
		}else{
			$adb->pquery("INSERT INTO sr_color_configuration(name,status,meta_key, meta_value) VALUES (?,?,?,?)",array($name,$check,$meta_key,$meta_value));
		}
		$response->setResult(array('success'=>true,'message'=>'success','url'=>$listview_URL));
		$response->emit();
	}	
	
}

