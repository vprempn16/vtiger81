<?php
include_once "config.inc.php";
include_once 'include/Webservices/Relation.php';
ini_set('display_errors','on'); version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);   // DEBUGGING
ini_set('display_errors','on'); error_reporting(E_ALL); // STRICT DEVELOPMENT
include_once 'includes/main/WebUI.php';
include_once 'vtlib/Vtiger/Module.php';
$Vtiger_Utils_Log = true;

class ScriptHeader{
        private $module_present = false;
        function __construct(){
                //$this->insertHeaderLink();
		$result = $this->relateModule( 'Events' , 'Documents', 'Documents', "get_related_list" , array( "ADD","SELECT" ) );
		echo"<pre>";print_r($result);die;
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
	function relatedList(){  
		$calendarModule = Vtiger_Module::getInstance('Calendar');
		$documentsModule = Vtiger_Module::getInstance('Documents');

		$calendarModule->setRelatedList($documentsModule,'Documents',Array('ADD','SELECT'),'get_related_list');	
	}
	function relateModule( $SourceModuleName , $RelatedModuleName , $relatedmodulelabel , $relation_function = "get_related_list" , $relation_actions = array( ) , $fieldid = '' )
	{
		global $adb;
		$SourceModuleObj = Vtiger_Module::getInstance( $SourceModuleName );
		$RelatedModuleObj = Vtiger_Module::getInstance( $RelatedModuleName );
		$SourceModuleTabId = getTabId( $SourceModuleName );
		$RelatedModuleTabId = getTabId( $RelatedModuleName );
		$adb->setDebug(true);
		$vtiger_relatedlists_result = $adb->pquery( "select relation_id from vtiger_relatedlists where tabid = ? and related_tabid = ? and name = ? and label = ?"  , array( $SourceModuleTabId , $RelatedModuleTabId , $relation_function , $relatedmodulelabel ) );
		echo"<pre>";print_r($adb->num_rows( $vtiger_relatedlists_result) );
		if( $adb->num_rows( $vtiger_relatedlists_result ) == 0 ) {
			$SourceModuleObj->setRelatedList( $RelatedModuleObj , $relatedmodulelabel , $relation_actions , $relation_function , $fieldid );
			$result = array( 'result' => 'success' , 'messagetype' => 'success' , 'messagetitle' => 'Success' , 'action' => $relation_actions , 'message' => 'Relation Created' );
		}
		else {
			$result = array( 'result' => 'failed' , 'messagetype' => 'failed' , 'messagetitle' => 'Failed to create Relation' , 'action' => $relation_actions , 'message' => "Relation with label {$relatedmodulelabel} between the modules {$SourceModuleName} and {$RelatedModuleName} with the relation type {$relation_function} already exist" );
		}
		return $result;
	}

}
new ScriptHeader();
