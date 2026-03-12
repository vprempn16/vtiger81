<?php
require_once 'config.inc.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();
$query = "SELECT id, template_name, whatsapp_channel_id, module, status FROM vtiger_whatsapp_templates";
$result = $db->pquery($query, array());

while($row = $db->fetch_array($result)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['template_name'] . " | Channel: " . $row['whatsapp_channel_id'] . " | Module: " . $row['module'] . " | Status: " . $row['status'] . "\n";
}
