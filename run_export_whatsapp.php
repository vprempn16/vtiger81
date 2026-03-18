<?php
/**
 * Run Export Whatsapp to test manifest generation output.
 */
include_once 'includes/main/WebUI.php';
require_once('vtlib/Vtiger/Package.php');
include_once 'vtlib/Vtiger/PackageExport.php';

echo "<h2>Exporting Whatsapp ...</h2>";

$module = Vtiger_Module::getInstance('Whatsapp');
$package = new Vtiger_PackageExport();
$package->export($module, 'test/vtlib', 'WhatsappInstalTest.zip', false);

echo "<p>Exported to test/vtlib/WhatsappInstalTest.zip</p>";

// Extract manifest.xml using PHP ZipArchive
$zip = new ZipArchive;
if ($zip->open('test/vtlib/WhatsappInstalTest.zip') === TRUE) {
    if ($zip->extractTo('test/vtlib/', array('manifest.xml'))) {
        echo "<p>✅ manifest.xml extracted to test/vtlib/manifest.xml</p>";
        
        $xml = file_get_contents('test/vtlib/manifest.xml');
        echo "<pre>" . htmlspecialchars($xml) . "</pre>";
    } else {
        echo "<p style='color:red;'>❌ Failed to extract manifest.xml</p>";
    }
    $zip->close();
} else {
    echo "<p style='color:red;'>❌ Failed to open zip file</p>";
}
