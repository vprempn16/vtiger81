<?php
class Settings_ServiceCompetency_GetUserWorkingDays_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);

        $recordModel = new Settings_ServiceCompetency_Record_Model();
        $records = $recordModel->getAllUsers();
        $working_days = $recordModel->getUserWorkingDays();
        $viewer->assign("WORKING_DAYS",$working_days);
        $viewer->assign("RECORDS",$records);
        $viewer->view("Workingdays.tpl", $qualifiedModuleName);
    }

}


?>
