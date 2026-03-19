<?php
class VDNotifierProConfig {
    public $generalSetting, $usersSetting, $moduleSetting;
    function __construct() {
        $this->ModuleSetting = $this->getModuleSetting();
        $this->GeneralSetting = Zend_Json::decode($this->getGeneralSetting()->Setting);
        $this->UsersSetting = Zend_Json::decode($this->getUsersSetting()->Setting);
        
    }
    public function getModuleSetting() {
         if (!is_file('modules/VDNotifierPro/Settings/ModuleSetting.php')) {
             include_once 'vtlib/Vtiger/Module.php';
             include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
             $Modules = Vtiger_Module_Model::getAll(array(0,2));
             $generate = new VDNotifierProGenerateSetting();
             if (!$generate->DefaultModuleSetting($Modules)){
                $response = new Vtiger_Response();
                $response->setError('201',vtranslate('ERROR_GENERAL_SETTING_CREATE', 'VDNotifierPro'));
                $response->emit();
                exit();
             }
         }
         include_once 'modules/VDNotifierPro/Settings/ModuleSetting.php';
         $module = (array)new VDNotifierProModuleSetting();
        
         return $module;
    }
    public function getUsersSetting() {
        global $current_user;
        $fileName = 'modules/VDNotifierPro/Settings/UsersSetting_'.$current_user->id.'.php';
         if (!is_file($fileName)) {
             include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
             $generate = new VDNotifierProGenerateSetting();
             if (!$generate->DefaultUsersSetting()){
                $response = new Vtiger_Response();
                $response->setError('201',vtranslate('ERROR_GENERAL_USERS_CREATE', 'VDNotifierPro'));
                $response->emit();
                exit();
             }
         }
         include_once $fileName;
         $ClassName = 'VDNotifierProUsersSetting_'.$current_user->id;
         return new $ClassName();
    }
    public function getGeneralSetting() {
         if (!is_file('modules/VDNotifierPro/Settings/GeneralSetting.php')) {
             include_once 'modules/VDNotifierPro/helpers/GenerateSetting.php';
             $generate = new VDNotifierProGenerateSetting();
             if (!$generate->DefaultGeneralSetting()){
                $response = new Vtiger_Response();
                $response->setError('201',vtranslate('ERROR_GENERAL_GENERAL_CREATE', 'VDNotifierPro'));
                $response->emit();
                exit();
             }
         }
         include_once 'modules/VDNotifierPro/Settings/GeneralSetting.php';
         return new VDNotifierProGeneralSetting();
    }
    
    
    
}