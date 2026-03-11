<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Whatsapp_Templates_View extends Settings_Vtiger_Index_View
{

    public function process(Vtiger_Request $request)
    {
        $qualifiedModuleName = $request->getModule(false);
        $channelId = $request->get('channel_id');

        $channels = Settings_Whatsapp_Record_Model::getAll($qualifiedModuleName);
        if (!$channelId && !empty($channels)) {
            $channelId = array_key_first($channels);
        }

        $templates = array();
        if ($channelId) {
            $templates = Settings_Whatsapp_Template_Model::getAllByChannel($channelId);
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('CHANNELS', $channels);
        $viewer->assign('SELECTED_CHANNEL_ID', $channelId);
        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_TAB', 'Templates');

        $viewer->view('Templates.tpl', $qualifiedModuleName);
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.Settings.Vtiger.resources.List",
            "modules.Settings.$moduleName.resources.Templates",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
