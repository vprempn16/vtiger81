<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_AtomsVariant_Record_Model extends Vtiger_Base_Model {

	public function getMenuItem() {
		$menuItem = Settings_Vtiger_MenuItem_Model::getInstance('Atom Variant Settings');
		return $menuItem;
	}

	public function getEditViewUrl($id = '') {
		$menuItem = $this->getMenuItem();
		if( $id == "" ) {
			return '?module=AtomsVariant&parent=Settings&view=Edit&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');;
		}
		else{
			return '?module=AtomsVariant&parent=Settings&view=Edit&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid').'&recordId='.$id;
		}
	}

    public function getLicenseViewUrl() {
        global $site_URL;
        $menuItem = $this->getMenuItem();
        return $site_URL.'index.php?module=AtomsVariant&parent=Settings&view=LicenseManagerEdit';
    }

	public function getListViewUrl() {
		$menuItem = $this->getMenuItem();
		return '?module=AtomsVariant&parent=Settings&view=List&block='.$menuItem->get('blockid').'&fieldid='.$menuItem->get('fieldid');
	}
    public function getFields($module){
        global $adb;
        $tabid = getTabId($module);
        $fields = array();
        $result = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid =? AND displaytype = ? ",array( $tabid,1));
        $num_rows = $adb->num_rows($result);
        if($num_rows > 0){
            for($i=0;$i<$num_rows; $i++){
                $field_name = $adb->query_result($result,$i,'fieldname');
                $field_label = $adb->query_result($result,$i,'fieldlabel');
                if($field_name !='' && $field_label != '' ){
                    $fields[$field_name] = $field_label;
                }
            }
        }
        return $fields;
    }
	public function getRecordDetails($id=""){
		global $adb;
		$columns = Array("variant_options");
		$retun = Array();
		
		$result = $adb->pquery("SELECT * FROM atom_variants_options ",array( ));
		$num_rows = $adb->num_rows($result);
		if($num_rows > 0){
			$options = $adb->query_result($result,0,'meta_value');
            $return['variant_fields'] = unserialize(base64_decode($options));
		}
		return $return;
		
	}
}



