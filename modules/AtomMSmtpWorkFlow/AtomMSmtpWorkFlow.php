<?php
class AtomMSmtpWorkFlow {

    function vtlib_handler($moduleName, $eventType) {
        global $adb;
        include_once 'modules/AtomMSmtpWorkFlow/AtomMSmtpWorkFlowCustomFile.php';
        $AtomMSmtpWorkFlowCustom = new AtomMSmtpWorkFlowCustom();
        if($eventType == 'module.postinstall') {
            $this->createCustomTables();
            $AtomMSmtpWorkFlowCustom->LicenseSettingsLink();
	    $AtomMSmtpWorkFlowCustom->postEnable();
        } else if($eventType == 'module.enabled') {
            $AtomMSmtpWorkFlowCustom->postEnable();
            $AtomMSmtpWorkFlowCustom->LicenseSettingsLink();
            $this->createCustomTables();
        } else if($eventType == 'module.disabled') {
            $AtomMSmtpWorkFlowCustom->postDisable();
        } else if($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($eventType == 'module.postupdate') {
            $this->createCustomTables();
            $AtomMSmtpWorkFlowCustom->LicenseSettingsLink();
	    $AtomMSmtpWorkFlowCustom->postEnable();
        }
    }
    function createCustomTables(){
        global $adb;

        $table_sql['atom_license_manager'] = "CREATE TABLE `atom_license_manager` (
                `id` int NOT NULL AUTO_INCREMENT,
                `meta_key` varchar(255) NOT NULL,
                `meta_value` longtext NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3";

        foreach($table_sql as $table_name => $sql){
            $table_exist_result = $adb->pquery("SHOW TABLES LIKE '$table_name'",array());
            $num_rows = $adb->num_rows($table_exist_result);

            if($num_rows == 0){
                $adb->pquery($sql,array());
            }

        }
        $adb->pquery("UPDATE vtiger_tab SET customized = 0 WHERE name = 'AtomPipeline'",array());
    }
}
