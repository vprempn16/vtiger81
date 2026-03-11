<?php
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

global $adb;

$moduleName = 'Whatsapp';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

if (!$moduleInstance) {
    die("Module $moduleName not found!\n");
}

echo "Base Table: " . $moduleInstance->basetable . "\n";

// Check table columns
$result = $adb->pquery("DESCRIBE " . $moduleInstance->basetable, array());
echo "Columns in " . $moduleInstance->basetable . ":\n";
while ($row = $adb->fetch_array($result)) {
    echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

// Check registered fields
$result = $adb->pquery("SELECT fieldname, columnname, uitype FROM vtiger_field WHERE tabid = ?", array(getTabId($moduleName)));
echo "\nRegistered Fields in vtiger_field:\n";
while ($row = $adb->fetch_array($result)) {
    echo "  - " . $row['fieldname'] . " (column: " . $row['columnname'] . ", uitype: " . $row['uitype'] . ")\n";
}

// Check picklist values for 'status' if it exists
$result = $adb->pquery("SELECT * FROM vtiger_whatsapp_status", array());
if ($result) {
    echo "\nPicklist values for 'status':\n";
    while ($row = $adb->fetch_array($result)) {
        echo "  - " . $row['status'] . "\n";
    }
} else {
    echo "\nTable vtiger_whatsapp_status does not exist.\n";
}
