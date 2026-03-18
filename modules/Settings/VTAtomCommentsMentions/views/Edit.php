<?php
class Settings_VTAtomCommentsMentions_Edit_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(false);
        $validator = new Settings_VTAtomCommentsMentions_LicenseManager_Model();
	
	$validator->getInstance($request);
        $is_validate = $validator->apiCall('validate');
        $is_active = $validator->apiCall('is_active');
        $licenseview_url = $validator->getLicenseViewUrl();

        if(!$is_validate['iskeyvalid']){
            header("Location:".$licenseview_url);
            exit();
        }
        if(!$is_active['iskeyactive']){
            header("Location:".$licenseview_url."&keyactive=false");
            exit();
        }
        $recordId = $request->get('recordId');
        $ischeckboxvalues = $this->getCheckBoxValue();
        $viewer->assign('RECORD',$ischeckboxvalues);
        $viewer->assign('recordId' , $recordId );
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $USERSLIST = Users_Record_Model::getAll();
        $viewer->assign("USERSLIST" , $USERSLIST );
        $viewer->view('Edit.tpl',$qualifiedName);
    }

    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('VT Atom Comments Addon Settings',$qualifiedModuleName);
    }
	function getCheckBoxValue(){
		global $adb;
		$sql  =  $adb->pquery('SELECT * FROM `atom_vtcommenton_rel`',array());
		if($adb->num_rows($sql) > 0){
			for($i=0;$i<$adb->num_rows($sql);$i++){
				$return[$adb->query_result($sql,$i,'type')] = $adb->query_result($sql,$i,'is_checked');
			}
		}
		return  $return;
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
