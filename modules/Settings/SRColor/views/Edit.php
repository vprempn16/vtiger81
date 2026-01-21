<?php

class Settings_SRColor_Edit_View extends Settings_Vtiger_Index_View {

        public function process(Vtiger_Request $request) {
                //ini_set('display_errors','off');
                $viewer = $this->getViewer($request);
                $qualifiedName = $request->getModule(false);
                $recordId = $request->get('recordId');
                $recordModel = new Settings_SRColor_Record_Model();
                if( $recordId == "" ) {
                        $record = $recordModel->getRecordDetails();
                }else{
                        $record = $recordModel->getRecordDetails($recordId);
                }
                $listview_URL = $recordModel->getListViewUrl();
                $meta_value = unserialize(base64_decode($record['meta_value']));
                $meta_key = $record['meta_key'];
                if($record['meta_value'] == ''){
                        $record['meta_value'] = Array();
                }
		//list($selected_module, $selected_field) = explode('_', $meta_key, 2);
		$selected_module = $meta_value['selected_module'];
		$selected_field = $meta_value['field'];
		$pickListField = $this->getPickListField($selected_module);
		$entityModule = $this->getEntitymodules();
		$pickListVal = $this->getPickListValueColor($selected_field,$selected_module);
		$getModuleSelectedField = $this->getModuleSelectedField($selected_module,$recordId);
		foreach($meta_value as $key => $value){
                        if (strpos($key, '_') !== false) {
                                $newKey = str_replace('_', ' ', $key);
                                $meta_value[$newKey] = $value;
                                unset($meta_value[$key]);
                        }
                }
		//echo"<pre>";print_r([$getModuleSelectedField]);echo"</pre>";die('ds');
		$viewer->assign('SELECTED_FIELDS',$getModuleSelectedField);
		$viewer->assign('ENTITY_MODULES',$entityModule);
		$viewer->assign('SELECTED_MODULE',$selected_module);
		$viewer->assign('SELECTED_FIELD',$selected_field);
		$viewer->assign('PICKLIST_FIELD',$pickListField);
		$viewer->assign('PICKLIST_VAL',$pickListVal);
                $viewer->assign('RECORD',$record);
                $viewer->assign('RECORDID',$recordId);
                $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
		$viewer->assign('META_VALUE', $meta_value);
                $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
                $USERSLIST = Users_Record_Model::getAll();
                $viewer->assign("USERSLIST" , $USERSLIST );
                $viewer->assign('LISTVIEWURL',$listview_URL);
                $viewer->view('Edit.tpl',$qualifiedName);
	}
	function getEntitymodules(){
		global $adb;
		$modules = array();
		$sql = $adb->pquery('SELECT * FROM vtiger_tab WHERE isentitytype = 1',array());
		if($adb->num_rows( $sql) > 0 ){
			while($row = $adb->fetchByAssoc($sql)){
				$modules[$row['name']] = $row['name'];
			}
		}
		return $modules;
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
	function getModuleSelectedField($selectedmodule,$id){
                global $adb;
                if($selectedmodule != ''){
                        $fields = array();
                        $sql = $adb->pquery("SELECT * FROM sr_color_configuration WHERE meta_key LIKE '%{$selectedmodule}_%' AND id != {$id} ",array());
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
	function getPickListValueColor($field,$selectedmodule){
                global $adb;
                if($field != '' && $selectedmodule != ''){
                        $sql = $adb->pquery("SELECT * FROM vtiger_{$field}",array());
                        $result = array();
                        if($adb->num_rows($sql) > 0){
                                while($row = $adb->fetch_array($sql)){
                                        $value = $row[$field];
                                        $result[$value] = $value;
                                }
                        }
                        return $result;
                }
        }
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.Settings.$moduleName.resources.SettingsEdit"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

}
