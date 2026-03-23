<?php

/* * *******************************************************************************
 * The content of this file is subject to the VD Notifier Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by vordoom.net are Copyright(C) vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */

class Settings_VDNotifierPro_Index_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$viewer = $this->getViewer($request);
		$allModules = Vtiger_Module_Model::getEntityModules();
//		$allModules = Settings_Workflows_Module_Model::getSupportedModules();
        $PBXManager = Vtiger_Module_Model::getInstance('PBXManager');
        $faq = Vtiger_Module_Model::getInstance('Faq');
        $allModules[$PBXManager->getId()] = $PBXManager;
        $allModules[$faq->getId()] = $faq;
        $modulesSeting = VDNotifierPro_Module_Model::getModulesSettings($allModules);
        $allModules = $modulesSeting['allmodules'];
		$globalSetting = $modulesSeting['config']->GeneralSetting;
		$setGlobal = Zend_Json::decode(file_get_contents('modules/VDNotifierPro/models/Setting.ini'));
		$viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('SETTING', $globalSetting );
		$viewer->assign('QUALIFIED_MODULE_NAME', $qualifiedModuleName);
		echo $viewer->view('Index.tpl', $qualifiedModuleName,true);
	}
}
