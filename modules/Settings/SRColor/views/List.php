<?php
class Settings_SRColor_List_View extends Settings_Vtiger_Index_View {

        public function process(Vtiger_Request $request) {
                $moduleName = $request->getModule();
                $qualifiedModuleName = $request->getModule(false);
		$viewer = $this->getViewer($request);
		$recordModel = new Settings_SRColor_Record_Model();
                $records = $recordModel->getListViewRecords();
		$listview_headers = $recordModel->getListViewHeaders();
                $editViewUrl = $recordModel->getEditViewUrl();
                $viewer = $this->getViewer($request);
                $viewer->assign("LISTVIEW_HEADERS",$listview_headers);
		$viewer->assign("LISTVIEW_ENTRIES_COUNT",$record_count);
                $viewer->assign("ADD_URL",$editViewUrl);
                $viewer->assign("RECORDS",$records);	
                $viewer->view("ListViewContents.tpl", $qualifiedModuleName);
        }

    /**
         * Function to get the list of Script models to be included
         * @param Vtiger_Request $request
         * @return <Array> - List of Vtiger_JsScript_Model instances
         */
        function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();

                $jsFileNames = array(
                        "modules.Settings.$moduleName.resources.SettingsList"
                );

                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
}



?>
