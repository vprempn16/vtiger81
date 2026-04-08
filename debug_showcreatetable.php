<?php
/**
 * Diagnostic script to test CreateTableSql
 */
include_once 'includes/main/WebUI.php';
global $adb;

echo "<h2>Testing CreateTableSql</h2>";

$tables = array('vtiger_whatsapp', 'vtiger_whatsappcf');

foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";
    $sql = Vtiger_Utils::CreateTableSql($table);
    if (empty($sql)) {
        echo "<p style='color:red;'>❌ CreateTableSql returned EMPTY string!</p>";
        
        // Manual check
        $res = $adb->pquery("SHOW CREATE TABLE $table", array());
        if ($res && $adb->num_rows($res) > 0) {
             echo "<p style='color:orange;'>⚠️ Manual SHOW CREATE TABLE worked! Vtiger_Utils failed. Manual output below:</p>";
             echo "<pre>" . htmlspecialchars($adb->query_result($res, 0, 1)) . "</pre>";
        } else {
             echo "<p style='color:red;'>❌ Manual SHOW CREATE TABLE ALSO FAILED!</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ CreateTableSql worked!</p>";
        echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    }
}
