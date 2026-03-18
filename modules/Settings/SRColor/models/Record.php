<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_SRColor_Record_Model extends Vtiger_Base_Model {

	public function getListViewHeaders(){

		$headers = Array(
					"Name",
					"Status",
					"Module",	
					"Field",	
					"Action",
				);
		return $headers;
	}

	public function getListViewRecords(){
		global $adb,$site_URL;
		$return = Array();
		$result = $adb->pquery("SELECT * FROM sr_color_configuration",array());
		$num_rows = $adb->num_rows($result);
		if($num_rows > 0){
			for($i=0;$i<$num_rows;$i++){
				$row = $i+1;
				$id = $adb->query_result($result,$i,'id');
				$return[$row]['id']	 = $id;
				$return[$row]['name'] = $adb->query_result($result,$i,'name');
				$status = $adb->query_result($result,$i,'status');
				$check = 'Off';
				if($status == 1){
					$check = "On";
				}
				$return[$row]['status'] = $check;
				$meta_key = $adb->query_result($result,$i,'meta_key');
				list($module, $field) = explode('_', $meta_key,2);
				$return[$row]['module'] = $module;
				//$return[$row]['field'] = $field;
				$return[$row]['field_label'] = $this->getFieldLabel($field);
				//$return[$row]['config_type'] = $adb->query_result($result,$i,'config_type');
				$return[$row]['editUrl'] = "$site_URL/index.php".$this->getEditViewUrl($id);
			}
		}
		return $return;

	}
	function getFieldLabel($field){
		global $adb;
		$field_label = '';
		if($field != ''){
			$query = "SELECT fieldname,fieldlabel FROM vtiger_field where fieldname=?";
			$result = $adb->pquery($query, array($field));
			if($adb->num_rows($result) > 0){
				$field_label = $adb->query_result($result,0,'fieldlabel');
			}
			return $field_label;
		}
	 }
	public function getMenuItem() {
                $menuItem = Settings_Vtiger_MenuItem_Model::getInstance('Color Settings');
                return $menuItem;
        }

        public function getEditViewUrl($id = '') {
                $menuItem = $this->getMenuItem();
		if( $id == "" ) {
                	return '?module=SRColor&parent=Settings&view=Edit&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');;
		}
		else{
                	return '?module=SRColor&parent=Settings&view=Edit&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid').'&recordId='.$id;
		}
        }

        public function getListViewUrl() {
                $menuItem = $this->getMenuItem();
                return '?module=SRColor&parent=Settings&view=List&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
        }
	
	public function getRecordDetails($id=""){
		global $adb;
		$columns = Array( "id","name" ,"status","meta_key","meta_value");
		$retun = Array();
		if( $id == "" ) {
			return $columns;
		}
		else{
			$result = $adb->pquery("SELECT * FROM sr_color_configuration WHERE id = ? ",array($id));
			$num_rows = $adb->num_rows($result);
			if($num_rows > 0){
				$return['id'] = $id;	
				foreach($columns as $column){
					$return[$column] = $adb->query_result($result,0,$column);
				}
			}
			return $return;
		}
	}



}
