<?php 
class BOMCustom{ 
	public function postEnable(){
		$this->addHeaderJs();
		$this->eventhandler();
	}
	public function postInstall(){
		$this->addHeaderJs();
		$this->eventhandler();
	}
	public function postDisable(){
		global $adb;
		$this->removeHeaderJs();
		$this->unregisterEventHandler();
	}
	public function postUpdate(){
		$this->addHeaderJs();                                                                                                                                                                         $this->eventhandler();
	}

	public function addHeaderJs(){
		global $adb;
		$linklabel = "BOMHeader";
		$linkurl = "layouts/v7/modules/BOM/resources/BOMHeader.js";
		$result = $adb->pquery("SELECT * FROM vtiger_links WHERE linklabel = ? AND linkurl = ? ",array($linklabel,$linkurl));
		$num_rows = $adb->num_rows($result);
		if($num_rows == 0){
			$moduleName='Home';
			$moduleInstance = Vtiger_Module::getInstance($moduleName);
			$moduleInstance->addLink('HEADERSCRIPT', $linklabel,$linkurl);
		}
	}
	public function removeHeaderJs(){
		$linklabel = "BOMHeader";
		$linkurl = "layouts/v7/modules/BOM/resources/BOMHeader.js";
		Vtiger_Link::deleteLink( 3 , 'HEADERSCRIPT' , $linklabel , $linkurl );
	}
	public function eventhandler(){
		$Vtiger_Utils_Log = true;
		include_once('vtlib/Vtiger/Event.php');
		Vtiger_Event::register('BOM', 'vtiger.entity.aftersave', 'BOMHandler', 'modules/BOM/BOMHandler.php');
	}
	public function unregisterEventHandler(){
		global $adb;
		$Vtiger_Utils_Log = true;
		include_once('include/events/VTEventsManager.inc');
		$class = 'BOMHandler';
		$result  = $adb->pquery('SELECT * FROM vtiger_eventhandlers WHERE handler_class =?',array($class));
		if($adb->num_rows($result) > 0){
			$eventsManager = new VTEventsManager($adb);
			$result = $eventsManager->unregisterHandler($class);
			return "success";
		}else{
			return "handler not found";
		}
	}

} 


