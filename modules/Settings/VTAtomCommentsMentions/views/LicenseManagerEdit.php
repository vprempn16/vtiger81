<?php
class Settings_VTAtomCommentsMentions_LicenseManagerEdit_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        $validator = new Settings_VTAtomCommentsMentions_LicenseManager_Model();

        $is_validate =  [];
        $is_active = [];

        $records = $validator->getRecordDetails();
        $license_key = $records['cmtmention_license_key'];
        $license_key = Vtiger_Functions::fromProtectedText($license_key);
        if($license_key !=''){ 
            $maskedKey = substr($license_key, 0, 4) . str_repeat('*', strlen($license_key) - 8) . substr($license_key, -4);
        }
        $is_validate = $validator->apiCall($license_key,'validate');
        $is_active = $validator->apiCall($license_key,'is_active');
        $iskeyactive = $is_active['iskeyactive'] ? true : false;
        $iskeyvalid = $is_validate['iskeyvalid'] ? true : false;
        $viewer->assign("API_KEY",$license_key);
        $viewer->assign("APIKEY",$maskedKey);
        $viewer->assign("IS_KEYVALID",$iskeyvalid);
        $viewer->assign("IS_KEYACTIVE",$iskeyactive);
        $viewer->assign("KEYACTIVE",$request->get('keyactive'));
        $viewer->assign("LICENSE_KEY",$license_key);
    
        $viewer->view("LicenseManagerEdit.tpl", $qualifiedModuleName);
    }
    
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
                "modules.Settings.$moduleName.resources.LicenseManager"
                );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }


}



?>
