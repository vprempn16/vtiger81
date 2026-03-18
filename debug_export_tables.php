<?php
/**
 * Diagnostic style script to trace export_Tables() node discovery.
 */
include_once 'includes/main/WebUI.php';
global $adb;

echo "<h2>Diagnostic focus table discovery</h2>";

$modulename = 'Whatsapp';
$moduleInstance = Vtiger_Module::getInstance($modulename);

if ($moduleInstance) {
    if ($moduleInstance->isentitytype) {
        $focus = CRMEntity::getInstance($modulename);
        vtlib_setup_modulevars($modulename, $focus);

        $tables = array($focus->table_name);
        if (!empty($focus->groupTable)) $tables[] = $focus->groupTable[0];
        if (!empty($focus->customFieldTable)) $tables[] = $focus->customFieldTable[0];

        echo "<h3>Tables Found for Export:</h3>";
        echo "<pre>";
        print_r($tables);
        echo "</pre>";
        
        echo "<h3>Internal Properties:</h3>";
        echo "table_name: " . ($focus->table_name ?? 'NOT SET') . "<br>";
        echo "customFieldTable: " . (isset($focus->customFieldTable) ? print_r($focus->customFieldTable, true) : 'NOT SET') . "<br>";
        
    } else {
        echo "<p style='color:red;'>Module is not an entity type!</p>";
    }
} else {
    echo "<p style='color:red;'>Module not found!</p>";
}
