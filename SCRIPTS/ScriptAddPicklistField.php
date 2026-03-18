<?php
chdir('../');
include_once 'vtlib/Vtiger/Module.php';
include_once "config.inc.php";

include_once 'includes/main/WebUI.php';
$Vtiger_Utils_Log = true;
class ScriptAddPicklistField{
	function __construct(){
	//$this->addPickListField();
    //$this->addFieldtest();
    //$this->relatedField();
    //$this->addInventoryFields();
    //$this->addNewFields();
    //$this->insertDummyValue();
	}
    function insertDummyValue(){
        global $adb;
        $adb->setDebug(true);
        $variantif = array('RPM'=>1100 ,'HP'=> 110 ,'Colour' =>'violet');
        $atomsvariantinfo = base64_encode(serialize($variantif));

        $adb->pquery("UPDATE `vtiger_inventoryproductrel` SET atomsvariantinfo = ?  WHERE `vtiger_inventoryproductrel`.`lineitem_id` = ?",array($atomsvariantinfo,37));
    }
    function relatedField(){
        $moduleName = 'AtomsVariant';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'Variant Information';
        $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
        $field = 'products';
        $fieldLabel = "Products";
        $field4 = Vtiger_Field::getInstance ( $field , $moduleInstance );
        if ( !$field4 ) {
            $field4 = new Vtiger_Field();
            $field4->name = $field;
            $field4->label = $fieldLabel ;
            $field4->column = $field;
            $field4->columntype = 'varchar(100)';
            $field4->uitype = 10;
            $field4->typeofdata = 'V~O';
            $blockInstance->addField($field4);
            $field4->setRelatedModules(Array('Products'));
            echo "<br>Field {$fieldLabel} with fieldname {$field} Created in {$MODULENAME} module";
        }
        echo'ok<br>';
    }
    function addNewFields(){
        $moduleName = $MODULENAME = 'AtomsVariant';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'Variant Information';
        $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
        
        $field1 = Vtiger_Field::getInstance ( "model" , $moduleInstance );
        if(!$field1){
            $field1 = new Vtiger_Field();
            $field1->name = "model";
            $field1->label = "Model";
            $field1->column = "model";
            $field1->columntype = 'VARCHAR(200)';
            $field1->uitype = 1;
            $field1->typeofdata = 'V~O';
            $blockInstance->addField($field1);
            echo "<br>Field Model  with fieldname model Created in {$MODULENAME} module";
        }
    
        $field2 = Vtiger_Field::getInstance ( "rpm" , $moduleInstance );
        if(!$field2){
            $field2 = new Vtiger_Field();
            $field2->name = "rpm";
            $field2->label = "RPM";
            $field2->column = "rpm";
            $field2->columntype = 'VARCHAR(200)';
            $field2->uitype = 1;
            $field2->typeofdata = 'V~O';
            $blockInstance->addField($field2);
            echo "<br>Field RPM  with fieldname rpm Created in {$MODULENAME} module";
        }
        $field3 = Vtiger_Field::getInstance ( "hp" , $moduleInstance );
        if(!$field3){
            $field3 = new Vtiger_Field();
            $field3->name = "hp";
            $field3->label = "HP";
            $field3->column = "hp";
            $field3->columntype = 'VARCHAR(200)';
            $field3->uitype = 1;
            $field3->typeofdata = 'V~O';
            $blockInstance->addField($field3);
            echo "<br>Field HP  with fieldname hp Created in {$MODULENAME} module";
        }
        $field4 = Vtiger_Field::getInstance ( "colour" , $moduleInstance );
        if(!$field4){
            $field4 = new Vtiger_Field();
            $field4->name = "colour";
            $field4->label = "Colour";
            $field4->column = "colour";
            $field4->columntype = 'VARCHAR(200)';
            $field4->uitype = 1;
            $field4->typeofdata = 'V~O';
            $blockInstance->addField($field4);
            echo "<br>Field Colour  with fieldname colour Created in {$MODULENAME} module";
        }
          echo'ok<br>';
    }
    function addFieldtest(){
        $moduleName = 'Inventory';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_ITEM_DETAILS';
        $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
        $field = 'variant';
        $fieldLabel = "Variant";
        $field4 = Vtiger_Field::getInstance ( $field , $moduleInstance );
        if ( !$field4 ) {
            $field4 = new Vtiger_Field();
            $field4->name = $field;
            $field4->label = $fieldLabel ;
            $field4->column = $field;
            $field4->columntype = 'VARCHAR(255)';
            $field4->table  ='vtiger_inventoryproductrel';
            $field4->uitype = 10;
            $blockInstance->addField($field4);
            echo "<br>Field {$fieldLabel} with fieldname {$field} Created in {$MODULENAME} module";
        }
        echo'ok<br>';
    }
    function addInventoryFields(){
        $moduleName = $MODULENAME = 'Invoice';
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_ITEM_DETAILS';
        $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
        $field = 'atomsvariantid';
        $fieldLabel = "Variant Id";
        $field1 = Vtiger_Field::getInstance ($field , $moduleInstance );
        if ( !$field1 ) {
            $field1 = new Vtiger_Field();
            $field1->name = $field;
            $field1->label = $fieldLabel ;
            $field1->column = $field;
            $field1->columntype = 'VARCHAR(100)';
            $field1->table  ='vtiger_inventoryproductrel';
            $field1->uitype = 1;
            $blockInstance->addField($field1);
            echo "<br>Field {$fieldLabel} with fieldname {$field} Created in {$MODULENAME} module";
        }

        $field2 = Vtiger_Field::getInstance ("atomsvariantinfo", $moduleInstance );
        if ( !$field2 ) {
            $field2 = new Vtiger_Field();
            $field2->name = "atomsvariantinfo";
            $field2->label = "Variant Info";
            $field2->column = "atomsvariantinfo";
            $field2->columntype = 'VARCHAR(250)';
            $field2->table  ='vtiger_inventoryproductrel';
            $field2->uitype = 21;
            $blockInstance->addField($field2);
            echo "<br>Field Variant Info with fieldname atomsvariantinfo Created in {$MODULENAME} module";
        }
        echo'ok<br>';
    }
	function addPickListField(){
                // Module Instance
                $moduleName= 'Leads';
                $moduleInstance = Vtiger_Module::getInstance($moduleName);
                $blockInstance = new Vtiger_Block();
                $blockInstance->label = 'LBL_LEAD_INFORMATION';
                $blockInstance = $blockInstance->getInstance($blockInstance->label,$moduleInstance);
                // Add new Field
                $pickListFieldName = "dead_status";
                $pickListFieldLabel = "Dead Status";
                $field4 = Vtiger_Field::getInstance ( $pickListFieldName , $moduleInstance );
                if ( !$field4 ) {
                        $field4 = new Vtiger_Field();
                        $field4->name = $pickListFieldName;
                        $field4->label = $pickListFieldLabel ;
                        $field4->column = $pickListFieldName;
                        $field4->columntype = 'VARCHAR(255)';
                        $field4->uitype = 15;
                        // Uitype 15 for Role based and 16 for No Role based Picklist   -       33 for Multi Select Combo Box
                        $field4->typeofdata = 'V~O~LE~128';
                        $field4->setPicklistValues( array( "DO NOT CALL","ETHNIC","INVALID","WRONG NUMBER","DIDN'T REQUEST INFO","UNDER AGE (-30)","FUCK OFF","NO ENGLISH","HUNG UP","NO MONEY","SPOKE, NOT FRONTED" ));
                        $blockInstance->addField($field4);
                        echo "<br>Field {$pickListFieldLabel} with fieldname {$pickListFieldName} Created in {$MODULENAME} module";
                }
                echo"ok";
        }

}
$ScriptAddPicklistField =  new ScriptAddPicklistField();
?>
