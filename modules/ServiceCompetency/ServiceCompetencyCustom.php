<?php 

class ServiceCompetencyCustom{
    public function postInstall(){
        $this->eventHandler();
        $this->RolePriceSettings();
        $this->headerLink();
        $this->addInventoryField();
        $this->createCustomFields();
        $this->createCustomTables();
        $this->relateModule("SalesOrder","ServiceContracts" ,"Service Contracts" ,'get_dependents_list' , array('ADD'));
    }
    public function postDisable(){
        $this->removeHeaderJs();
        $this->unregisterEventHandler();
        $this->removeSettingsLink();
    }
    public function postEnable(){
        $this->headerLink();
        $this->eventHandler();
        $this->RolePriceSettings();
    }
    public function postUpdate(){
        $this->eventHandler();
        $this->RolePriceSettings();
        $this->headerLink();
        $this->addInventoryField();
        $this->createCustomFields();
        $this->createCustomTables();
        $this->relateModule("SalesOrder","ServiceContracts" ,"Service Contracts" ,'get_dependents_list' , array('ADD'));
    }
    function eventHandler(){
        include_once('vtlib/Vtiger/Event.php');
        Vtiger_Event::register('Vtiger', 'vtiger.entity.beforesave', 'ServiceCompetencyHandler', 'modules/ServiceCompetency/ServiceCompetencyHandler.php');
        Vtiger_Event::register('Vtiger', 'vtiger.entity.aftersave', 'ServiceCompetencyHandler', 'modules/ServiceCompetency/ServiceCompetencyHandler.php');
        Vtiger_Event::register('Vtiger', 'vtiger.entity.afterdelete', 'ServiceCompetencyHandler', 'modules/ServiceCompetency/ServiceCompetencyHandler.php');
    }
    function unregisterEventHandler(){
        global $adb;
        $Vtiger_Utils_Log = true;
        include_once('include/events/VTEventsManager.inc');
        $class = 'ServiceCompetencyHandler';
        $result  = $adb->pquery('SELECT * FROM vtiger_eventhandlers WHERE handler_class =?',array($class));
        if($adb->num_rows($result) > 0){
            $eventsManager = new VTEventsManager($adb);
            $result = $eventsManager->unregisterHandler($class);
            return "success";
        }else{
            return "handler not found";
        }
    }
    function RolePriceSettings(){
        global $adb;
        $name = "Service Competency Settings";
        $description = "Configure Service Competency Settings";
        $linkto = "index.php?parent=Settings&module=ServiceCompetency&view=Edit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
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
    public function removeSettingsLink(){
        global $adb;
        $name = "Role Pricing";
        $description = "Configure Role Pricing";
        $linkto = "index.php?parent=Settings&module=ServiceCompetency&view=Edit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
        $num_rows = $adb->num_rows($result);
        if($num_rows == 1) {
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name = ?", array($name));
        }
    }
    function headerLink(){
        global $adb;
        $linklabel = "ServiceCompetencyHeader";
        $linkurl = "layouts/v7/modules/ServiceCompetency/resources/ServiceCompetencyHeader.js";
        $result = $adb->pquery("SELECT * FROM vtiger_links WHERE linklabel = ? AND linkurl = ? ",array($linklabel,$linkurl));
        $num_rows = $adb->num_rows($result);
        if($num_rows == 0){
            $moduleName='Home';
            $moduleInstance = Vtiger_Module::getInstance($moduleName);
            $moduleInstance->addLink('HEADERSCRIPT', $linklabel,$linkurl);
        }
    } 
    function removeHeaderJs(){
        $linklabel = "ServiceCompetencyHeader";
        $linkurl = "layouts/v7/modules/ServiceCompetency/resources/ServiceCompetencyHeader.js";
        Vtiger_Link::deleteLink( 3 , 'HEADERSCRIPT' , $linklabel , $linkurl );
    }
    function relateModule( $SourceModuleName , $RelatedModuleName , $relatedmodulelabel , $relation_function = "get_related_list" , $relation_actions = array( ) , $fieldid = '' ){
        global $adb;
        $SourceModuleObj = Vtiger_Module::getInstance( $SourceModuleName );
        $RelatedModuleObj = Vtiger_Module::getInstance( $RelatedModuleName );
        $SourceModuleTabId = getTabId( $SourceModuleName );
        $RelatedModuleTabId = getTabId( $RelatedModuleName );
        $vtiger_relatedlists_result = $adb->pquery( "select relation_id from vtiger_relatedlists where tabid = ? and related_tabid = ? and name = ? and label = ?"  , array( $SourceModuleTabId , $RelatedModuleTabId , $relation_function , $relatedmodulelabel ) );
        if( $adb->num_rows( $vtiger_relatedlists_result ) == 0 ) {
            $SourceModuleObj->setRelatedList( $RelatedModuleObj , $relatedmodulelabel , $relation_actions , $relation_function , $fieldid );
            $result = array( 'result' => 'success' , 'messagetype' => 'success' , 'messagetitle' => 'Success' , 'action' => $relation_actions , 'message' => 'Relation Created' );
        }
        else {
            $result = array( 'result' => 'failed' , 'messagetype' => 'failed' , 'messagetitle' => 'Failed to create Relation' , 'action' => $relation_actions , 'message' => "Relation with label {$relatedmodulelabel} between the modules {$SourceModuleName} and {$RelatedModuleName} with the relation type {$relation_function} already exist" );
        }
        return $result;
    }
    function addInventoryField(){
        $modules = ["SalesOrder"];
        foreach($modules as $module){
            $moduleName = $MODULENAME = $module;
            $moduleInstance = Vtiger_Module::getInstance($moduleName);
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = 'LBL_ITEM_DETAILS';
            $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
            $field4 = Vtiger_Field::getInstance("consultantname",$moduleInstance);
            if(!$field4 ){
                $field4 = new Vtiger_Field();
                $field4->name = "consultantname";
                $field4->label = "Consultant Name";
                $field4->column = "consultantname";
                $field4->columntype = 'VARCHAR(250)';
                $field4->table  ='vtiger_inventoryproductrel';
                $field4->uitype = 1;
                $blockInstance->addField($field4);
            }

            $field5 = Vtiger_Field::getInstance("servicecompetencyid",$moduleInstance);
            if(!$field5 ){
                $field5 = new Vtiger_Field();
                $field5->name = "servicecompetencyid";
                $field5->label = "Service Name";
                $field5->column = "servicecompetencyid";
                $field5->columntype = 'VARCHAR(250)';
                $field5->table  ='vtiger_inventoryproductrel';
                $field5->uitype = 1;
                $blockInstance->addField($field5);
            }
            $field6 = Vtiger_Field::getInstance("consultant_startdate",$moduleInstance);
            if(!$field6 ){
                $field6 = new Vtiger_Field();
                $field6->name = "consultant_startdate";
                $field6->label = "Consultant Start Date";
                $field6->column = "consultant_startdate";
                $field6->table  ='vtiger_inventoryproductrel';
                $field6->uitype = 5;
                $field6->typeofdata ='D~O';
                $field6->displaytype = 0;
                $blockInstance->addField($field6);
            }
            $field6 = Vtiger_Field::getInstance("consultant_enddate",$moduleInstance);
            if(!$field6 ){
                $field6 = new Vtiger_Field();
                $field6->name = "consultant_enddate";
                $field6->label = "Consultant End Date";
                $field6->column = "consultant_enddate";
                $field6->table  ='vtiger_inventoryproductrel';
                $field6->uitype = 5;
                $field6->typeofdata ='D~O';
                $field6->displaytype = 0;
                $blockInstance->addField($field6);
            }
        }
    }
    function createCustomFields(){
        //Sales Order field
        $MODULENAME = 'SalesOrder';
        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);
        if ($moduleInstance){
            $blocklabel = 'LBL_SO_INFORMATION';
            $block = Vtiger_Block::getInstance( $blocklabel, $moduleInstance );
            $field4 = Vtiger_Field::getInstance ( 'startdate', $moduleInstance );
            if(!$field4 ){
                $field4 = new Vtiger_Field();
                $field4->name = "startdate";
                $field4->label = "Start Date";
                $field4->column = "startdate";
                $field4->uitype = 5;
                $field4->typeofdata = 'D~O';
                $block->addField($field4);
            }
        }
        // ServiceContracts field
        $MODULENAME = 'ServiceContracts';
        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);
        if ($moduleInstance){
            $blocklabel = 'LBL_SERVICE_CONTRACT_INFORMATION';
            $block = Vtiger_Block::getInstance( $blocklabel, $moduleInstance );
            $field1 = Vtiger_Field::getInstance ( 'permandaycost', $moduleInstance );
            if(!$field1 ){
                $field1 = new Vtiger_Field();
                $field1->name = "permandaycost";
                $field1->label = "Per manday Cost";
                $field1->column = "permandaycost";
                $field1->uitype = 1;
                $field1->typeofdata = 'N~O';
                $block->addField($field1);
            }
            $field2 = Vtiger_Field::getInstance ( 'servicename', $moduleInstance );
            if(!$field2 ){
                $field2 = new Vtiger_Field();
                $field2->name = "servicename";
                $field2->label = "Service Name";
                $field2->column = "servicename";
                $field2->uitype = 10;
                $field2->typeofdata = 'V~O';
                $block->addField($field2);
                $field2->setRelatedModules( array( 'Services' ) );
            }
            $field3 = Vtiger_Field::getInstance ( 'sc_related_to', $moduleInstance );
            $field3->setRelatedModules( array( 'SalesOrder' ) );
            $field4 = Vtiger_Field::getInstance('cf_1471',$moduleInstance);
            if(!$field4 ){
                $field4 = new Vtiger_Field();
                $field4->name = "cf_1471";
                $field4->label = "Role";
                $field4->column = "cf_1471";
                $field4->uitype = 15;
                $field4->typeofdata = 'V~O';
                $block->addField($field4);
                $field4->setPicklistValues( array("Project Manager","Reviewer","Implementer","Learner","Not Started"));
            }
        }
        // Tickets Fields
        $MODULENAME = 'HelpDesk';
        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);
        if ($moduleInstance){
            $blocklabel = 'LBL_TICKET_INFORMATION';
            $block = Vtiger_Block::getInstance( $blocklabel, $moduleInstance );
            $field1 = Vtiger_Field::getInstance( 'cf_792', $moduleInstance );
            if(!$field1 ){
                $field1 = new Vtiger_Field();
                $field1->name = "cf_792";
                $field1->label = "Ticket Date";
                $field1->column = "cf_792";
                $field1->uitype = 5;
                $field1->typeofdata = 'D~O';
                $field1->table  ='vtiger_ticketcf';
                $block->addField($field1);
            }
        }
    }
     function createCustomTables(){
        global $adb;

        $table_sql['atom_role_pricing'] = "CREATE TABLE `atom_role_pricing` (
                `id` int NOT NULL AUTO_INCREMENT,
                `meta_key` varchar(100) NOT NULL,
                `meta_value` longtext NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3";

        $table_sql['sc_userworkingdays'] = "CREATE TABLE `sc_userworkingdays` (
                `userid` int NOT NULL,
                `working_days` int NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3";

        $table_sql['scid_rel'] = "CREATE TABLE `scid_rel` (
                `crmid` int NOT NULL,
                `rel_id` int NOT NULL,
                `module` varchar(100) DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3"; 

        foreach($table_sql as $table_name => $sql){
            $table_exist_result = $adb->pquery("SHOW TABLES LIKE '$table_name'",array());
            $num_rows = $adb->num_rows($table_exist_result);
            if($num_rows == 0){
                $adb->pquery($sql,array());
            }
        }
        $adb->pquery("UPDATE vtiger_tab SET customized = 0 WHERE name = 'ServiceCompetency'",array());
    }

}



?>
