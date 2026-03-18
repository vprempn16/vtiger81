<?php
/**
 * Diagnostic script to check isentitytype for Whatsapp
 */
include_once 'includes/main/WebUI.php';
global $adb;

$result = $adb->pquery("SELECT isentitytype, tabid FROM vtiger_tab WHERE name = ?", array('Whatsapp'));

if ($adb->num_rows($result) > 0) {
    $isentitytype = $adb->query_result($result, 0, 'isentitytype');
    echo "<h2>Module: Whatsapp</h2>";
    echo "<p>isentitytype: <b>$isentitytype</b> (Expected: 1 for Entity module)</p>";
    
    if ($isentitytype == 0) {
        echo "<p style='color:red;'>⚠️ This module is registered as an EXTENSION (isentitytype=0), so PackageExport skips creating base tables!</p>";
    } else {
        echo "<p style='color:green;'>✅ Module is registered as Entity module.</p>";
    }
} else {
    echo "<h2>Module Whatsapp not found in vtiger_tab!</h2>";
}
