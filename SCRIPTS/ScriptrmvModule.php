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
         //       $this->rmvModule();
		$this->rmvField();
        }
        function rmvModule(){
                $module = Vtiger_Module::getInstance('TSWhatsapp');
                echo"<pre>";print_r($module);echo"</pre>";
                if ($module) $module->delete(); echo "Module Deleted.";
        }
	function rmvField(){
		$fields = array('publisheddate');
		$moduleInstance = new Vtiger_Module();
		$modules =   ["PromotionalMaterial"];
		foreach($modules as $module){
			$moduleInstance = Vtiger_Module::getInstance ($module );
			foreach($fields as $field){
				$fieldInstance = Vtiger_Field::getInstance ( $field, $moduleInstance );
				if ($fieldInstance) {
					$fieldInstance->delete(); echo "Deleted field.";
					echo $field."Deleted Done...";
				} else {
					echo "Field Not found. <br>";
				}
			}
		}
	}
}
$customAction =  new ScriptrmvModule();
?>
