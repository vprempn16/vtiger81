<?php
$root = dirname(__FILE__);
require_once $root . '/include/main/WebUI.php';
$db = PearDatabase::getInstance();
$logFile = 'storage/wa_validation.log';
file_put_contents($logFile, "DB Check for Packaging:\n", FILE_APPEND);

file_put_contents($logFile, "Entity Names Sample:\n", FILE_APPEND);
$res = $db->pquery("SELECT * FROM vtiger_entityname LIMIT 3", array());
while ($row = $db->fetch_array($res)) {
    file_put_contents($logFile, json_encode($row) . "\n", FILE_APPEND);
}

file_put_contents($logFile, "WS Entity Sample:\n", FILE_APPEND);
$res = $db->pquery("SELECT * FROM vtiger_ws_entity LIMIT 3", array());
while ($row = $db->fetch_array($res)) {
    file_put_contents($logFile, json_encode($row) . "\n", FILE_APPEND);
}

unlink(__FILE__);
