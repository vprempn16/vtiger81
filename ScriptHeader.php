<?php
include_once "config.inc.php";
include_once 'include/Webservices/Relation.php';
include_once 'includes/main/WebUI.php';
include_once 'vtlib/Vtiger/Module.php';
$Vtiger_Utils_Log = true;

class ScriptHeader{
        private $module_present = false;
        function __construct(){
                $this->insertHeaderLink();
        }

        function insertHeaderLink(){
            global $adb;
            $linklabel = "DocumentUpload";
            $linkurl = "layouts/v7/modules/Contacts/resources/DocumentUpload.js";
            $result = $adb->pquery("SELECT * FROM vtiger_links WHERE linklabel = ? AND linkurl = ? ",array($linklabel,$linkurl));
            $num_rows = $adb->num_rows($result);
            if($num_rows == 0){
                $moduleName='Home';
                $moduleInstance = Vtiger_Module::getInstance($moduleName);
                $moduleInstance->addLink('HEADERSCRIPT', $linklabel,$linkurl);
                echo("Header Script Added<br/>");
            }else{
                echo("Header Script Already Present<br/>");
            }
         }

}
new ScriptHeader();
