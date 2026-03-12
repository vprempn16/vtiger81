<?php
require_once 'config.inc.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();
$query = "SELECT id, template_name, language, whatsapp_channel_id, module, status FROM vtiger_whatsapp_templates";
$result = $db->pquery($query, array());

$templates = array();
while($row = $db->fetch_array($result)) {
    $templates[] = array(
        'id' => $row['id'],
        'name' => $row['template_name'],
        'channel' => $row['whatsapp_channel_id'],
        'module' => $row['module'],
        'status' => $row['status'],
        'language' => $row['language']
    );
}
echo json_encode($templates, JSON_PRETTY_PRINT);
