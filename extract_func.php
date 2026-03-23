<?php
$file = "/var/www/html/vtiger81/modules/ServiceCompetency/ServiceCompetencyHandler.php";
$lines = file($file);
$output = [];
$capture = false;

foreach ($lines as $index => $line) {
    if (strpos($line, "createTicketsForServiceContract") !== false) {
        $capture = true;
    }
    if ($capture) {
        $output[] = $line;
        if (count($output) > 200) break; 
    }
}

file_put_contents("/var/www/html/vtiger81/handler_func.json", json_encode($output, JSON_PRETTY_PRINT));
echo "Done!\n";
