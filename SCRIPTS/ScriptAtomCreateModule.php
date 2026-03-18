<?php
chdir('../');
include_once "config.inc.php";
include_once 'include/Webservices/Relation.php';

include_once 'includes/main/WebUI.php';
include_once 'vtlib/Vtiger/Module.php';
$Vtiger_Utils_Log = true;

class ScriptAtomCreateModule{
        private $module_present = false;

        function __construct(){
                //$this->process();
                //$this->createVariantModule();   
                //$this->settingsLink();
                //$this->insertHeaderLink();
               //$result = $this->relateModule("Products" , "AtomsVariant" , "Variant related list","get_dependents_list");
               //echo "<pre>";print_r( $result );
               //$this->eventhandler();
        }
        function eventhandler(){
            $Vtiger_Utils_Log = true;
            include_once('vtlib/Vtiger/Event.php');
            Vtiger_Event::register('Vtiger', 'vtiger.entity.aftersave', 'AtomsVariant', 'modules/AtomsVariant/AtomsVariantHandler.php');
            echo "successful";

        }
        function insertHeaderLink(){
            global $adb;
            $linklabel = "AtomsVariantHeader";
            $linkurl = "layouts/v7/modules/AtomsVariant/resources/AtomsVariantHeader.js";
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
        function settingsLink(){
            global $adb;
            $name ="Atom Variant Settings";
            $description = "Configure Variant Options";
            $linkto = "index.php?parent=Settings&module=AtomsVariant&view=Edit";
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
        function createVariantModule(){
            $Vtiger_Utils_Log = true;

            $MODULENAME = 'AtomsVariant';


            $moduleInstance = Vtiger_Module::getInstance($MODULENAME);
            if ($moduleInstance || file_exists('modules/'.$MODULENAME)){
                echo "Module already present - choose a different name.";
            }
            else
            {
                $moduleInstance = new Vtiger_Module();
                $moduleInstance->name = $MODULENAME;
                $moduleInstance->label = 'Atoms Variant';
                $moduleInstance->parent= 'Tools';
                $moduleInstance->save();

                // Schema Setup
                $moduleInstance->initTables();

                // Field Setup
                $block = new Vtiger_Block();
                $block->label = 'Variant Information';
                $moduleInstance->addBlock($block);



                $field1  = new Vtiger_Field();
                $field1->name = 'variant_name';
                $field1->label= 'Variant Name';
                $field1->uitype= 1;
                $field1->column = $field1->name;
                $field1->columntype = 'VARCHAR(100)';
                $field1->typeofdata = 'V~M';
                $block->addField($field1);
                
                $field2  = new Vtiger_Field();
                $field2->name = 'variant_price';
                $field2->label= 'Price';
                $field2->uitype= 72;
                $field2->column = $field2->name;
                $field2->columntype = 'decimal(25,8)';
                $field2->summaryfield = 0;
                $field2->typeofdata = 'N~O';
                $field2->displaytype= 1;
                $block->addField($field2);

                $moduleInstance->setEntityIdentifier($field1);

                $mfield1 = new Vtiger_Field();
                $mfield1->name = 'assigned_user_id';
                $mfield1->label = 'Assigned To';
                $mfield1->table = 'vtiger_crmentity';
                $mfield1->column = 'smownerid';
                $mfield1->uitype = 53;
                $mfield1->typeofdata = 'V~M';
                $block->addField($mfield1);

                $mfield2 = new Vtiger_Field();
                $mfield2->name = 'createdtime';
                $mfield2->label= 'Created Time';
                $mfield2->table = 'vtiger_crmentity';
                $mfield2->column = 'createdtime';
                $mfield2->uitype = 70;
                $mfield2->typeofdata = 'DT~O';
                $mfield2->displaytype= 2;
                $block->addField($mfield2);

                $mfield3 = new Vtiger_Field();
                $mfield3->name = 'modifiedtime';
                $mfield3->label= 'Modified Time';
                $mfield3->table = 'vtiger_crmentity';
                $mfield3->column = 'modifiedtime';
                $mfield3->uitype = 70;
                $mfield3->typeofdata = 'DT~O';
                $mfield3->displaytype= 2;
                $block->addField($mfield3);

                /* NOTE: Vtiger 7.1.0 onwards */
                $mfield4 = new Vtiger_Field();
                $mfield4->name = 'source';
                $mfield4->label = 'Source';
                $mfield4->table = 'vtiger_crmentity';
                $mfield4->displaytype = 2; // to disable field in Edit View
                $mfield4->quickcreate = 3;
                $mfield4->masseditable = 0;
                $block->addField($mfield4);

                $mfield5 = new Vtiger_Field();
                $mfield5->name = 'starred';
                $mfield5->label = 'starred';
                $mfield5->table = 'vtiger_crmentity_user_field';
                $mfield5->displaytype = 6;
                $mfield5->uitype = 56;
                $mfield5->typeofdata = 'C~O';
                $mfield5->quickcreate = 3;
                $mfield5->masseditable = 0;
                $block->addField($mfield5);

                $mfield6 = new Vtiger_Field();
                $mfield6->name = 'tags';
                $mfield6->label = 'tags';
                $mfield6->displaytype = 6;
                $mfield6->columntype = 'VARCHAR(1)';
                $mfield6->quickcreate = 3;
                $mfield6->masseditable = 0;
                $block->addField($mfield6);
                /* End 7.1.0 */

                // Filter Setup
                $filter1 = new Vtiger_Filter();
                $filter1->name = 'All';
                $filter1->isdefault = true;
                $moduleInstance->addFilter($filter1);
                $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2);

                // Sharing Access Setup
                $moduleInstance->setDefaultSharing();

                // Webservice Setup
                $moduleInstance->initWebservice();

                mkdir('modules/'.$MODULENAME);
                echo "OK\n";
            }
        }
        function relateModule( $SourceModuleName , $RelatedModuleName , $relatedmodulelabel , $relation_function = "get_related_list" , $relation_actions = array( ) ){
            global $adb;
            $SourceModuleObj = Vtiger_Module::getInstance( $SourceModuleName );
            $RelatedModuleObj = Vtiger_Module::getInstance( $RelatedModuleName );
            $SourceModuleTabId = getTabId( $SourceModuleName );
            $RelatedModuleTabId = getTabId( $RelatedModuleName );
            $vtiger_relatedlists_result = $adb->pquery( "select relation_id from vtiger_relatedlists where tabid = ? and related_tabid = ? and name = ? and label = ?"  , array( $SourceModuleTabId , $RelatedModuleTabId , $relation_function , $relatedmodulelabel ) );
            if( $adb->num_rows( $vtiger_relatedlists_result ) == 0 ) {
                $SourceModuleObj->setRelatedList( $RelatedModuleObj , $relatedmodulelabel , $relation_actions , $relation_function );
                $result = array( 'result' => 'success' , 'messagetype' => 'success' , 'messagetitle' => 'Success' , 'action' => $relation_actions , 'message' => 'Relation Created' );
            }
            else {
                $result = array( 'result' => 'failed' , 'messagetype' => 'failed' , 'messagetitle' => 'Failed to create Relation' , 'action' => $relation_actions , 'message' => "Relation with label {$relatedmodulelabel} between the modules {$SourceModuleName} and {$RelatedModuleName} with the relation type {$relation_function} already exist" );
            }
            return $result;
        }
        function process(){
                $MODULENAME = 'AtomsPerformanceTracker';
                $moduleInstance = Vtiger_Module::getInstance($MODULENAME);
                if ($moduleInstance) {
                        echo "Module already present<br/>";
                        $this->module_present = true;
                } else {
                        $moduleInstance = new Vtiger_Module();
                        $moduleInstance->name = $MODULENAME;
                        $moduleInstance->parent= 'Tools';
                        $moduleInstance->save();
			
                        $blocklabel = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
                        $block = Vtiger_Block::getInstance( $blocklabel, $moduleInstance );
                        if( !$block )
                        {
                                $block = new Vtiger_Block();
                                $block->label = $blocklabel;
                                $moduleInstance->addBlock($block);
			}
			 $field1 = Vtiger_Field::getInstance ( 'title' , $moduleInstance );
                        if ( !$field1 ) {
                                $field1  = new Vtiger_Field();
                                $field1->name = 'title';
                                $field1->label= 'Title';
                                $field1->uitype= 1;
                                $field1->column = $field1->name;
                                $field1->columntype = 'VARCHAR(200)';
                                $field1->typeofdata = 'V~M';
                                $block->addField($field1);
                                echo "Title field created<br>";
                        } else {
                                echo "Title already present<br>";
                        }

                        $moduleInstance->setEntityIdentifier($field1);
                        // Sharing Access Setup
                        $moduleInstance->setDefaultSharing();

                        // Webservice Setup
                        $moduleInstance->initWebservice();
                        $this->module_present = true;

                        echo "Module Created<br/>";
                }

	}
}


$ScriptAtomCreateModule = new ScriptAtomCreateModule();
