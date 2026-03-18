<?php

if (!file_exists("vendor/autoload.php")) {
    echo "Please install composer dependencies.";
    exit;
}

//error_reporting(E_ALL);ini_set("display_errors" , "on");

//Overrides GetRelatedList : used to get related query
//TODO : Eliminate below hacking solution
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

require_once('vtlib/Vtiger/Package.php');
include_once 'vtlib/Vtiger/PackageExport.php';

$module = Vtiger_Module::getInstance('Products');
$package = new Vtiger_PackageExport();
$package->export($module, 'test/vtlib', 'ProductsInstal1.8.zip', true);
echo "<pre>";
print_r($package);
die('#');
