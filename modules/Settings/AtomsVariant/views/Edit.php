<?php
class Settings_AtomsVariant_Edit_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
            
        $recordModel = new Settings_AtomsVariant_Record_Model();
        $records = $recordModel->getRecordDetails();
        $editViewUrl = $recordModel->getEditViewUrl();
        $fields = $recordModel->getFields($moduleName);

        $viewer->assign("FIELDS",$fields);
        $viewer->assign("LISTVIEW_HEADERS",$listview_headers);
        $viewer->assign("LISTVIEW_ENTRIES_COUNT",$record_count);
        $viewer->assign("ADD_URL",$editViewUrl);
        $viewer->assign("RECORDS",$records);
        $viewer->assign("SELECTED_FIELDS",$records['variant_fields']);
    
        $viewer->view("Edit.tpl", $qualifiedModuleName);
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



?>
