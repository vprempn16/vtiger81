<?php


include_once "config.inc.php";

include_once 'includes/main/WebUI.php';
$Vtiger_Utils_Log = true;
class customAction{

        function __construct(){
    //           	$this->rmvModule();
        }

        function rmvModule(){
                $module = Vtiger_Module::getInstance('AtomMSmtpWorkFlow');
                echo"<pre>";print_r($module);echo"</pre>";
                if ($module) $module->delete(); echo "Module Deleted.";
	}
}
$customAction =  new customAction();
?>
