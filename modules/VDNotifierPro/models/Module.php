<?php
/* * *******************************************************************************
 * The content of this file is subject to the VD Notifier Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */
require_once 'modules/VDNotifierPro/helpers/Setting.php';
include_once('vtlib/Vtiger/Module.php');
include_once 'include/Webservices/Query.php';
class VDNotifierPro_Module_Model extends Vtiger_Module_Model {
       
        public function getModulesSettings($allModules){
           
            $config = new VDNotifierProConfig();
            
            foreach ($allModules as $key=>$module){
                      $nameModule = $module->getName();
                      if (isset($config->ModuleSetting[$nameModule])){
                            $module->setting = Zend_Json::decode($config->ModuleSetting[$nameModule]);
                      }
                      else 
                          $module->setting = array ("status"=>"0","creator"=>"0","owner"=>"0","modif"=>"0","newentity"=>"0","updateentity"=>"0");
                      
            }
            
            return array('allmodules'=>$allModules,'config'=>$config);
        } 
       
        static function getSetting($moduleName) {
            $config = new VDNotifierProConfig();
            if (isset($config->ModuleSetting[$moduleName])){
                            $Setting = Zend_Json::decode($config->ModuleSetting[$moduleName]);
                      }
                      else {
                          $Setting = array ("status"=>"0","creator"=>"0","owner"=>"0","modif"=>"0","newentity"=>"0","updateentity"=>"0");
                      }
            $Setting['key'] = $config->GeneralSetting['k'];
           
           
            return $Setting;
            
        }
        
}