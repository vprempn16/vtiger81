<?php
include_once 'config.php';
include_once 'include/database/PearDatabase.php';

global $adb;
$adb = PearDatabase::getInstance();

echo "--- TABLE STRUCTURE ---" . PHP_EOL;
$res = $adb->pquery("SHOW COLUMNS FROM vtiger_whatsapp", array());
if ($res) {
    while ($row = $adb->fetch_array($res)) {
        echo $row['Field'] . " | " . $row['Type'] . PHP_EOL;
    }
} else {
    echo "Table vtiger_whatsapp not found or error accessing it." . PHP_EOL;
}

echo PHP_EOL . "--- REGISTERED FIELDS ---" . PHP_EOL;
$tabIdResult = $adb->pquery("SELECT tabid FROM vtiger_tab WHERE name = 'Whatsapp'", array());
if ($adb->num_rows($tabIdResult) > 0) {
    $tabid = $adb->query_result($tabIdResult, 0, 'tabid');
    $res = $adb->pquery("SELECT fieldname, columnname, uitype FROM vtiger_field WHERE tabid = ?", array($tabid));
    while ($row = $adb->fetch_array($res)) {
        echo $row['fieldname'] . " (col: " . $row['columnname'] . ") | uitype: " . $row['uitype'] . PHP_EOL;
    }
} else {
    echo "Module Whatsapp not found in vtiger_tab." . PHP_EOL;
}
