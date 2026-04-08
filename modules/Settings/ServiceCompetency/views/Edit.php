<?php 

class Settings_ServiceCompetency_Edit_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);

        $recordModel = new Settings_ServiceCompetency_Record_Model();
        $records = $recordModel->getRecordDetails();
        $roles = array("Not Started"=>"notstarted","Learner"=>"learner","Implementer"=>"implementer","Reviewer"=>"reviewer","Project Manager"=>"projectmanager");
        $viewer->assign("ROLES",$roles);
        $viewer->assign("RECORDS",$records);
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
