<?php
require_once 'config.inc.php';
require_once 'include/utils/utils.php';

$db = PearDatabase::getInstance();
$query = "SHOW COLUMNS FROM vtiger_whatsapp_template_map";
$result = $db->pquery($query, array());

while($row = $db->fetch_array($result)) {
    echo $row['Field'] . "\n";
}
