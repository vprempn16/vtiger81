<?php 

include_once "config.inc.php";
include_once 'include/Webservices/Relation.php';

include_once 'includes/main/WebUI.php';
include_once 'vtlib/Vtiger/Module.php';
$Vtiger_Utils_Log = true;

global $adb;
include_once( 'modules/com_vtiger_workflow/VTTaskManager.inc' );
$taskType = "VTWhatsappTask";
$result = $adb->pquery("SELECT * FROM com_vtiger_workflow_tasktypes where tasktypename=?",array($taskType));
if( $adb->num_rows($result) == 0 ) {
	$taskType = array("name"=>$taskType, "label"=>"Send Whatsapp Message", "classname"=>$taskType, "classpath"=>"modules/WhatsappIntergration/tasks/$taskType.php", "templatepath"=>"modules/WhatsappIntergration/Tasks/$taskType.tpl", "modules"=>$defaultModules, "sourcemodule"=>'WhatsappIntergration');
	VTTaskType::registerTaskType($taskType);
}
