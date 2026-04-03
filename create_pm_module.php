<?php

ini_set('display_errors', 'on');version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);   // DEBUGGING
ini_set('display_errors','on'); error_reporting(E_ALL); // STRICT DEVELOPMENT

//TODO : Eliminate below hacking solution
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';
include_once 'includes/main/WebUI.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Block.php';
require_once 'vtlib/Vtiger/Field.php';
require_once 'vtlib/Vtiger/Filter.php';


$moduleName = 'PromotionalMaterials';

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!$moduleInstance) {
	// Create new module
	$moduleInstance = new Vtiger_Module();
	$moduleInstance->name = $moduleName;
	$moduleInstance->label = 'Promotional Material';
	$moduleInstance->parent = 'Tools';

	$moduleInstance->save();
	$tabid = $moduleInstance->id;

	$moduleInstance->initTables();

	if (!$moduleInstance) {
		echo "Module instance not available";
	}

	$blockLabel = 'LBL_PROMOTIONAL_MATERIAL_INFORMATION';
	$blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);

	if (!$blockInstance) {
		$blockInstance = new Vtiger_Block();
		$blockInstance->label = $blockLabel;
		$moduleInstance->addBlock($blockInstance);
		echo " ✓ Block created: $blockLabel";
	} else {
		echo"  Block already exists: $blockLabel";
	}

	if (!$moduleInstance) {
		echo "Module instance not available";
	}

	$blockInstance = Vtiger_Block::getInstance('LBL_PROMOTIONAL_MATERIAL_INFORMATION', $moduleInstance);

	if (!$blockInstance) {
		echo "Block instance not found";
	}

	// Defensive check: Ensure basetable is set
	if (empty($moduleInstance->basetable)) {
		$moduleInstance->basetable = 'vtiger_promotionalmaterial';
	}

	$fields = array(
		'title' => array(
			'label' => 'Title',
			'uitype' => 1,
			'typeofdata' => 'V~M',
			'column' => 'title',
			'columntype' => 'VARCHAR(255)',
		),
		'description' => array(
			'label' => 'Description',
			'uitype' => 21,
			'column' => 'description',
			'typeofdata' => 'V~O',
			'columntype' => 'LONGTEXT',
		),
		'promotional_status' => array(
			'label' => 'Status',
			'uitype' => 16,
			'typeofdata' => 'V~O',
			'columntype' => 'VARCHAR(25)',
			'column' => 'promotional_status',
			'picklistvalues' => array('Draft', 'Published', 'Archived'),
		),
		'publisheddate' => array(
			'label' => 'Published Date',
			'uitype' => 5,
			'typeofdata' => 'D~O',
			'column' => 'publisheddate',
			'columntype' => 'DATETIME',
		),
		'promotional_document' => array(
			'label' => 'Document Upload',
			'uitype' => 28,
			'typeofdata' => 'V~O',
			'column' => 'promotional_document',
			'columntype' => 'VARCHAR(500)',
		),
		'assigned_user_id' => array(
			'label' => 'Assigned To',
			'uitype' => 53,
			'typeofdata' => 'V~M',
			'table' => 'vtiger_crmentity',
			'column' => 'smownerid',
		),
		'createdtime' => array(
			'label' => 'Created Time',
			'uitype' => 70,
			'typeofdata' => 'DT~O',
			'table' => 'vtiger_crmentity',
			'column' => 'createdtime',
			'displaytype' => 2,
		),
		'modifiedtime' => array(
			'label' => 'Modified Time',
			'uitype' => 70,
			'typeofdata' => 'DT~O',
			'table' => 'vtiger_crmentity',
			'column' => 'modifiedtime',
			'displaytype' => 2,
		),
		'source' => array(
			'label' => 'Source',
			'uitype' => 16,
			'typeofdata' => 'V~O',
			'table' => 'vtiger_crmentity',
			'column' => 'source',
			'displaytype' => 2,
		),
		'starred' => array(
			'label' => 'Starred',
			'uitype' => 56,
			'typeofdata' => 'C~O',
			'table' => 'vtiger_crmentity_user_field',
			'displaytype' => 6,
		),
		'tags' => array(
			'label' => 'Tags',
			'uitype' => 1,
			'typeofdata' => 'V~O',
			'column' => 'tags',
			'columntype' => 'VARCHAR(255)',
			'displaytype' => 6,
		)
	);

	foreach ($fields as $fieldName => $fieldInfo) {
			$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);

			if (!$fieldInstance) {
				$fieldInstance = new Vtiger_Field();
				$fieldInstance->name = $fieldName;
				$fieldInstance->label = $fieldInfo['label'];
				$fieldInstance->table = isset($fieldInfo['table']) ? $fieldInfo['table'] : $moduleInstance->basetable;
				$fieldInstance->column = $fieldInfo['column'];

				// Set columntype only for custom fields (not system fields)
				if (isset($fieldInfo['columntype']) && !isset($fieldInfo['table'])) {
					$fieldInstance->columntype = $fieldInfo['columntype'];
				}

				$fieldInstance->uitype = $fieldInfo['uitype'];
				$fieldInstance->typeofdata = $fieldInfo['typeofdata'];

				// Set display type if specified (for system fields like createdtime, modifiedtime, source)
				if (isset($fieldInfo['displaytype'])) {
					$fieldInstance->displaytype = $fieldInfo['displaytype'];
				}

				// Add field to block
				$blockInstance->addField($fieldInstance);
				echo "  ✓ Field created: $fieldName";

				// Set picklist values if defined
				if (isset($fieldInfo['picklistvalues'])) {
					$fieldInstance->setPicklistValues($fieldInfo['picklistvalues']);
					echo " → Picklist values set for: $fieldName";
				}
				if($fieldName == 'title'){ 
					$moduleInstance->setEntityIdentifier($fieldInstance);
				}
			} else {
				echo "  ⚠ Field already exists: $fieldName";
			}
	}
	$field1 = Vtiger_Field::getInstance('title', $moduleInstance);
	$field2 = Vtiger_Field::getInstance('promotional_status', $moduleInstance);	    		
	$field3 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);

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
} else {
	$tabid = $moduleInstance->id;
	echo" Module already exists: {$moduleName} (ID: {$tabid})";
}
