<?php
/* * *******************************************************************************
 * The content of this file is subject to the VD VDNotifier Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */



require_once 'modules/VDNotifierPro/helpers/Setting.php';


class VDNotifierPro_ActivityVDNotifier_Action extends Vtiger_Action_Controller{

	function __construct() {

		$this->exposeMethod('getVDNotifier');
		$this->exposeMethod('postpone');
        $this->exposeMethod('setSeting');
        $this->exposeMethod('deleteVDNotifier');
        $this->exposeMethod('changeVDNotifier');
        $this->exposeMethod('setting');
        $this->exposeMethod('setSetingUser');
        $this->exposeMethod('setSetingGlob');
        $this->exposeMethod('installDomen');
        $this->exposeMethod('deleteDomen');
        $this->exposeMethod('cleanMessage');
        $this->exposeMethod('deleteVDPopunder');
        $this->exposeMethod('removeVDPopunder');
        $this->exposeMethod('removeVDNotifier');
        $this->exposeMethod('changeVDPopunder');
        $this->exposeMethod('VDTranslate');
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}
        public function deleteVDPopunder($request){
            $id = $request->get('id');

            VDNotifierPro_Record_Model::cleanReminder($id);
        }
        public function changeVDPopunder($request){
            $id = $request->get('id');

            VDNotifierPro_Record_Model::cleanReminder($id,5);
        }

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}

	function getVDNotifier(Vtiger_Request $request) {
        global $adb, $current_user;
        $record = VDNotifierPro_Record_Model::findAll();
		$response = new Vtiger_Response();
		$response->setResult($record);
		$response->emit();
	}

	function postpone(Vtiger_Request $request) {
		$id = $request->get('id');
		VDNotifierPro_Record_Model::updateReminder($id);
	}
        function setSeting(Vtiger_Request $request) {
            global $adb, $current_user;
			$field = $request->get('field');
                if ($field == 'status'){
                    $this->changeAll($request);
                }
                else {
                    $value = (int)$request->get('value');
                    $module = $request->get('moduleId');
                    include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
                    $generate = new VDNotifierProGenerateSetting();
                    $generate->SetValue('ModuleSetting',$module,array($field=>$value,'status'=>'1'));
                }

	}

	function VDTranslate(Vtiger_Request $request)
    {
        $curLanguage = $_SESSION['authenticated_user_language'];
        if (!$curLanguage) {
            $curLanguage = 'en_US';
        }
//        $key = $request->get('str');
//        $result = Vtiger_Language_Handler::getJSTranslatedString($key, 'VDNotifierPro', $curLanguage);
        $result = Vtiger_Language_Handler::getModuleStringsFromFile($curLanguage, 'VDNotifierPro');
        $response = new Vtiger_Response();
        $response->setResult($result['jsLanguageStrings']);
        $response->emit();
    }

        function changeAll($request){
            global $adb,$current_user;
            $value = $request->get('value');
            $module = $request->get('moduleId');
            include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
            $generate = new VDNotifierProGenerateSetting();
            $generate->ChangeModuleValue($module,$value);
        }

        function deleteVDNotifier(Vtiger_Request $request){
            $id = $request->get('id');
            VDNotifierPro_Record_Model::update($id);

        }

    function removeVDNotifier(Vtiger_Request $request){
        $id = $request->get('id');
        VDNotifierPro_Record_Model::removing($id);

    }

        function changeVDNotifier(Vtiger_Request $request){
            $id = $request->get('id');
            VDNotifierPro_Record_Model::update($id,5);

        }

        function cleanMessage(Vtiger_Request $request){

            if (VDNotifierPro_Record_Model::clean()){
                $response = new Vtiger_Response();
                $response->setResult('');
                $response->emit();
            }
        }

        function setting(Vtiger_Request $request){
             global $adb, $current_user;
             $this->config = new VDNotifierProConfig();
             $module = $request->getModule();
             $id = $current_user->id;
             $result = new stdClass();
             $result->ajax = $this->config->GeneralSetting['a'];
             $result->time = $this->config->GeneralSetting['t'];
             $result->sound = (bool)$this->config->UsersSetting['sound'];
             $result->message = (bool)$this->config->UsersSetting['message'];
             $response = new Vtiger_Response();
             $response->setResult($result);
             $response->emit();
        }

        function newUserSetting($result){
            include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
            $Generate = new GenerateSetting();
            return $Generate->DefaultUsersSetting();
        }
        function setSetingUser(Vtiger_Request $request){
            $field = $request->get('field');
            $value = $request->get('value');
            include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
            $generate = new VDNotifierProGenerateSetting();
            $generate->SetValue('UsersSetting','Setting',array($field=>$value));

        }
        function setSetingGlob(Vtiger_Request $request){
            $array = array();
            $array['a'] = $request->get('a');
            $array['t'] = $request->get('t');
            include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
            $generate = new VDNotifierProGenerateSetting();
            $generate->SetValue('GeneralSetting','Setting',$array);

        }
        function installDomen(Vtiger_Request $request){
             $this->license = new ProtectedLicense('VDNotifierPro');
             $result = $this->license->setDomen($request->get('k'));

             $response = new Vtiger_Response();
             $response->setResult($result);
             $response->emit();
        }
        function deleteDomen(Vtiger_Request $request){
             $this->license = new ProtectedLicense('VDNotifierPro');
             $result = $this->license->unsetDomen($request->get('k'));

             $response = new Vtiger_Response();
             $response->setResult($result);
             $response->emit();
        }
}

