<?php
chdir('../');
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
$Vtiger_Utils_Log = true;
class ScriptrmvModule{

        function __construct(){
                $this->rmvModule();
        }
        function rmvModule(){
                $module = Vtiger_Module::getInstance('TSWhatsapp');
                echo"<pre>";print_r($module);echo"</pre>";
                if ($module) $module->delete(); echo "Module Deleted.";
        }

}
$customAction =  new ScriptrmvModule();
?>
