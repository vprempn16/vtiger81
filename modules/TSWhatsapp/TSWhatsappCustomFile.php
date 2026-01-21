<?php 

class TSWhatsappCustomFile{
	function postInstall(){
        	$this->insertHeaderLink();
	}
	function postEnable(){
        	$this->insertHeaderLink();
	}
	function postDisable(){
		$this->removeHeaderJs();
	}
	function postUpdate(){
        	$this->insertHeaderLink();
	}

	function insertHeaderLink(){
            global $adb;
            $linklabel = "TSWhatsappHeader";
            $linkurl = "layouts/v7/modules/TSWhatsapp/resources/WhatsappIntegrationHeader.js";
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
	public function removeHeaderJs(){
		$linklabel = "TSWhatsappHeader";
		$linkurl = "layouts/v7/modules/TSWhatsapp/resources/WhatsappIntegrationHeader.js";
		Vtiger_Link::deleteLink( 0 , 'HEADERSCRIPT' , $linklabel , $linkurl );
	}
}
