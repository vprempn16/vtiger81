<?php
class SRColor {

        function vtlib_handler($moduleName, $eventType) {
                global $adb;
                if($eventType == 'module.postinstall') {
                        $this->SettingsLink();
                        $this->insertHeaderLink();
                        $this->createTable();
                } else if($eventType == 'module.enabled') {
                        $this->SettingsLink();
                        $this->insertHeaderLink();
                } else if($eventType == 'module.disabled') {
                } else if($eventType == 'module.preuninstall') {
                        // TODO Handle actions when this module is about to be deleted.
                } else if($eventType == 'module.preupdate') {
                        // TODO Handle actions before this module is updated.
                } else if($eventType == 'module.postupdate') {
                        $this->SettingsLink();
                        $this->insertHeaderLink();
                        $this->createTable();
                }
	}
	function SettingsLink(){
                global $adb;
                $name = "Color Settings";
                $description = "Configure Status Colors";
                $linkto = "index.php?parent=Settings&module=SRColor&view=List";
                $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
                error_log( "SELECT * FROM vtiger_settings_field WHERE name= '{$name}'" , 3 , "/tmp/SRColor.log" );
                $num_rows = $adb->num_rows($result);
                if($num_rows == 0) {
                        $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_OTHER_SETTINGS'));
                        $otherSettingsBlockCount = $adb->num_rows($otherSettingsBlock);

                        if ($otherSettingsBlockCount > 0) {
                                $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
                                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks WHERE blockid=?", array($blockid));
                                if ($adb->num_rows($sequenceResult)) {
                                        $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
                                }
                        }

                        $fieldid = $adb->getUniqueID('vtiger_settings_field');
                        $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active , pinned) VALUES(?,?,?,?,?,?,?,?,?)", array($fieldid, $blockid, $name, '',$description, $linkto, $sequence++, 0 , 1));

                        $adb->pquery("UPDATE vtiger_settings_field_seq SET id = ?",array($fieldid));
                }
	}
	function createTable(){
		global $adb;
		$tbl_name = "sr_color_configuration";
                $table_sql[$tbl_name] = "CREATE TABLE `{$tbl_name}` ( `id` INT(19) NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL , `status` VARCHAR(5) NOT NULL , `meta_key` VARCHAR(255) NOT NULL , `meta_value` LONGTEXT NOT NULL , PRIMARY KEY (`id`));";
		foreach($table_sql as $table_name => $sql){
			$table_exist_result = $adb->pquery("SHOW TABLES LIKE '$table_name'",array());

			$num_rows = $adb->num_rows($table_exist_result);   
			if($num_rows == 0){  	
				$adb->pquery($sql,array());    
			}  
		}
		$adb->pquery("UPDATE vtiger_tab SET customized = 0 WHERE name = 'SRColor'",array());     

	}
	function insertHeaderLink(){
                global $adb;
                $linklabel = "ColorHeader";
                $linkurl = "layouts/v7/modules/SRColor/resources/ColorHeader.js";
                $result = $adb->pquery("SELECT * FROM vtiger_links WHERE linklabel = ? AND linkurl = ? ",array($linklabel,$linkurl));
                $num_rows = $adb->num_rows($result);
                if($num_rows == 0){
                        $moduleName='Home';
                        $moduleInstance = Vtiger_Module::getInstance($moduleName);
                        $moduleInstance->addLink('HEADERSCRIPT', $linklabel,$linkurl);
                }
        }

}
