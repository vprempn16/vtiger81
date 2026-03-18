<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_SRColor_getModuleFieldValues_Action extends Settings_Vtiger_Basic_Action {

	public function process(Vtiger_Request $request) {
		global $current_user;
		$category = $request->get('category');
		$response = new Vtiger_Response();
		$response->setResult(array('success'=>false));
		if($category != ''){
			if($category == 'get_fields'){
				$module = $request->get('currentVal');
				$picklistfields = $this->getPickListField($module);
				$getModuleSelectedField = $this->getModuleSelectedField($module);
				$response->setResult(array('success'=>true,'selectedFields'=>$getModuleSelectedField,'picklistfields'=>json_encode($picklistfields,true)));
			}
			if($category == 'get_fields_values' ){
				$field = $request->get('currentVal');
				$selectedmodule = $request->get('selectedmodule');
				$picklistrow = $this->getPickListrow($field,$selectedmodule,$request);
				if($picklistrow != ''){
					$response->setResult(array('success'=>true,'picklistvalues'=>json_encode($picklistrow['picklistvalues'],true),'picklistrow'=>json_encode($picklistrow['html'],true)));
				}
			}
		}
		$response->emit();
	}
	function getModuleSelectedField($selectedmodule){
		global $adb;
		if($selectedmodule != ''){
			$fields = array();
			$sql = $adb->pquery("SELECT * FROM sr_color_configuration WHERE meta_key LIKE '%{$selectedmodule}_%'",array());
			if($adb->num_rows($sql) > 0){
				for($i=0;$i<$adb->num_rows($sql);$i++){
					$meta_key =  $adb->query_result($sql,$i,'meta_key');
					list($selectedmodule,$field) = explode('_', $meta_key, 2);
                                        $fields[$i] = $field;
				}
			}
			return $fields;
		}	
	}		
	function getPickListField($module){
		global $adb;
		if($module != ''){
			$moduleId = getTabId($module);
			$query = "SELECT fieldname,fieldlabel
				FROM vtiger_field INNER JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid
				WHERE vtiger_tab.name = '{$module}' AND uitype in ('15','16','33')
				AND vtiger_field.presence in (0,2)
				AND fieldname != 'hdnTaxType'
				AND fieldname != 'campaignrelstatus'";
			$result = $adb->pquery($query, array());
			$picklistFields = array();
			while ($row = $adb->fetch_array($result)) {
				$fieldName = $row['fieldname'];
				$fieldLabel = $row['fieldlabel'];
				$picklistFields[$fieldName] = $fieldLabel;
			}
		}
		return $picklistFields;
	}
	function getPickListrow($field,$selectedmodule,$request){
		global $adb;
		if($field != '' && $selectedmodule != ''){
			$viewer = $this->getViewer($request);
			$moduleName = $request->getModule();
                	$qualifiedModuleName = $request->getModule(false);
			$sql = $adb->pquery("SELECT * FROM vtiger_{$field}",array());	
			$html = '';
			$module_field = $selectedmodule.'_'.$field;
			$que =  $adb->pquery("SELECT * FROM sr_color_configuration WHERE meta_key = ?",array($module_field));
			if($adb->num_rows($que) > 0){
				$meta_key = $adb->query_result($que,0,'meta_key');
				$meta_value = $adb->query_result($que,0,'meta_value');
				$meta_value = unserialize(base64_decode($meta_value));
				foreach($meta_value as $key => $value){
					if (strpos($key, '_') !== false) {
						$newKey = str_replace('_', ' ', $key);
						$meta_value[$newKey] = $value;
						unset($meta_value[$key]);
					}
				}
			}
			if($adb->num_rows($sql) > 0){
				while($row = $adb->fetch_array($sql)){
					$value = $row[$field];
					$result[$value] = $value;
					$html .='<tr>
      	  						<td class="fieldLabel" style="width:53%;">
               		 					<label>'.$value.'</label>
        						</td>
 				       			<td class=" fieldValue" >
        				       			<div class=" col-lg-6 col-md-6 col-sm-12">
									<div class="color-picker" data-color-element="'.$value.'" value="'.$meta_value[$value].'"></div>
                                                                	<input type="hidden" name="'.$value.'" id="'.$value.'" value="'.$meta_value[$value].'">
                						</div>
       	 						</td>
						</tr>';
				}
			}
			//$viewer->assign("RESULT",$result);
                	return array('html'=>$html,'picklistvalues'=>$result);	
		}
	}
}

?>

