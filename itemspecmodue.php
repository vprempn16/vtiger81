<?php 


require_once('config.inc.php');
require_once('includes/Loader.php');

// Import Vtiger classes for module creation
require_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Block.php');
require_once('vtlib/Vtiger/Field.php');

$Vtiger_Utils_Log = true;

$MODULENAME = 'Itemspec';


$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || file_exists('modules/'.$MODULENAME))
{
    echo "Module already present - choose a different name.";
}
else
{
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->label = 'Item Spec';
    $moduleInstance->parent= 'Tools';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();

    // Field Setup
    $block = new Vtiger_Block();
    $block->label = 'Item Spec Information';
    $moduleInstance->addBlock($block);



    $field1  = new Vtiger_Field();
    $field1->name = 'title';
    $field1->label= 'Title';
    $field1->uitype= 1;
    $field1->column = $field1->name;
    $field1->columntype = 'VARCHAR(100)';
    $field1->typeofdata = 'V~M';
    $block->addField($field1);

    $moduleInstance->setEntityIdentifier($field1);


    $field2  = new Vtiger_Field();
    $field2->name = 'spectitle';
    $field2->label= 'Spec Title';
    $field2->uitype= 1;
      $field2->column = $field2->name;
      $field2->columntype = 'VARCHAR(100)';
      $field2->typeofdata = 'V~O';
      $block->addField($field2);


       $field3  = new Vtiger_Field();
       $field3->name = 'specdetails';
       $field3->label= 'Spec Details';
       $field3->uitype= 19;
       $field3->column = $field3->name;
       $field3->columntype = 'VARCHAR(255)';
       $field3->typeofdata = 'V~O';
       $block->addField($field3);

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
