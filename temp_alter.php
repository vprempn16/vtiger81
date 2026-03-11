<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.inc.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$db = PearDatabase::getInstance();
$db->pquery("DROP TABLE IF EXISTS vtiger_whatsapp_template_map", array());
$query = "CREATE TABLE IF NOT EXISTS vtiger_whatsapp_template_map (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT,
    template_language VARCHAR(50),
    template_variable VARCHAR(100),
    component_type VARCHAR(50),
    button_index INT,
    crm_module VARCHAR(50),
    crm_field VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$db->pquery($query, array());
$check = $db->pquery("DESCRIBE vtiger_whatsapp_template_map", array());
$cols = [];
while ($row = $db->fetch_array($check)) {
    $cols[] = $row['Field'] . ' ' . $row['Type'];
}
echo implode(', ', $cols);
