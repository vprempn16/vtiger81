<?php

/* * *******************************************************************************
 * The content of this file is subject to the Notificator Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */
class VDNotifierProGenerateSetting {
    
     public function DefaultModuleSetting($Modules){
        $params = array ("status"=>"1","creator"=>"1","owner"=>"1","modif"=>"1","newentity"=>"1","updateentity"=>"1");
        $ModulesSettings = array();
        foreach ($Modules as $module){
            if ($module->get('isentitytype') == '1' && $module->getName() != 'Webmails'){
                $ModulesSettings[$module->getName()] = array();
                foreach ($params as $name => $value){
                    $ModulesSettings[$module->getName()][$name] = $value;
                }
            }
        }
        return $this->SetSetting('ModuleSetting', $ModulesSettings);
    }
    
     public function DefaultUsersSetting(){
        global $current_user;
       
        $ClassName = "UsersSetting_".$current_user->id;
        $UserSettings = array();
        $UserSettings['Setting'] = array ("sound"=>"1","message"=>"1");
        return  $this->SetSetting($ClassName, $UserSettings);
    }
     public function DefaultGeneralSetting(){
        
        $GeneralSetting = array();
        $GeneralSetting['Setting'] = array ("a"=>"1","t"=>"5000","k"=>"");
        return  $this->SetSetting("GeneralSetting", $GeneralSetting);
    }
    public function SetValue($NameObject,$NameSetting,$array){
        global $current_user;
        $config = new VDNotifierProConfig();
        
        $data = $config->$NameObject;
        $_data = array();
        
        if ($NameObject == 'ModuleSetting'){
            foreach ($data as $key=>$_val){
                $_data[$key] = Zend_Json::decode($_val);
            }
            
        }
        else {
            $_data[$NameSetting] = $data;
        }
        
        foreach ($array as $key=>$value){
            $_data[$NameSetting][$key] = $value;
        }
        
        if ($NameObject == 'UsersSetting'){
            $NameObject = "UsersSetting_".$current_user->id;
        }
        //print_r ($data);die;
        return  $this->SetSetting($NameObject, $_data);
        
    }
    public function ChangeModuleValue($module,$value){
        $config = new VDNotifierProConfig();
        $data = $config->ModuleSetting;
        $notFound = true;
        foreach ($data as $key=>$_val){
                $data[$key] = Zend_Json::decode($_val);
                if ($key == $module){
                    $notFound = false;
                    foreach ($data[$module] as $_key => $val){
                        $data[$module][$_key] = $value;
                    }
                }
            }
        if ($notFound) {
            $data[$module]['status'] = $value;
            $data[$module]['creator'] = $value;
            $data[$module]['owner'] = $value;
            $data[$module]['modif'] = $value;
            $data[$module]['newentity'] = $value;
            $data[$module]['updateentity'] = $value;
        }
        
        return  $this->SetSetting('ModuleSetting', $data);
    }
    
    public function GenerateClass($ClassName, $array){
        $text = "<?php \n";
        $text .= "class VDNotifierPro$ClassName { \n";
        foreach ($array as $key=> $value){
            $text .= "\tpublic $".$key." = '".Zend_Json::encode($value)."';\n";
        }
        $text .= '}';
        return $text;
    }
    
    public function SetSetting($ClassName, $array){
        $text = $this->GenerateClass($ClassName, $array);
        $fp = fopen('modules/VDNotifierPro/Settings/'.$ClassName.'.php', 'w');
        $result = fwrite($fp, $text);
        fclose($fp);
        return $result;
    }
}