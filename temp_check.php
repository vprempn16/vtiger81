<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.inc.php';
//require_once 'includes/Loader.php';
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vimport('includes.runtime.EntryPoint');

$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT template_name, components FROM vtiger_whatsapp_templates", array());
$templates = array();
for ($i = 0; $i < $db->num_rows($result); $i++) {
    $row = $db->query_result_rowdata($result, $i);
    $templates[] = array('name' => $row['template_name'], 'components' => $row['components']);
}
echo json_encode($templates, JSON_PRETTY_PRINT);
