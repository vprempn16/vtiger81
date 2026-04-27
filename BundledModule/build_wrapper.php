<?php
$baseDir = __DIR__;
$installerDir = $baseDir . "/MyMassInstaller";
if (!is_dir($installerDir))
    mkdir($installerDir);

$moduleSrcDir = $installerDir . "/modules/MyMassInstaller";
if (!is_dir($installerDir . "/modules"))
    mkdir($installerDir . "/modules");
if (!is_dir($moduleSrcDir))
    mkdir($moduleSrcDir);
if (!is_dir($moduleSrcDir . "/packages"))
    mkdir($moduleSrcDir . "/packages");

$zips = ["ready_PromotionalMaterial.zip", "ready_VDNotifierPro_2.4.0.zip", "ready_VTAtomCommentsMentionsNon-License-1.3.zip", "ready_BranchUsers1.1.zip"];
foreach ($zips as $zip) {
    if (file_exists($baseDir . "/" . $zip)) {
        $originalName = str_replace("ready_", "", $zip);
        copy($baseDir . "/" . $zip, $moduleSrcDir . "/packages/" . $originalName);
    }
}
$manifest = <<<XML
<?xml version="1.0"?>
<module>
    <name>MyMassInstaller</name>
    <label>My Mass Installer</label>
    <parent>Tools</parent>
    <type>extension</type>
    <version>1.0</version>
    <dependencies>
        <vtiger_version>8.0.0</vtiger_version>
    </dependencies>
</module>
XML;
file_put_contents($installerDir . "/manifest.xml", $manifest);

$php = <<<PHP
<?php
class MyMassInstaller {
    public function vtlib_handler(\$modulename, \$event_type) {
        if (\$event_type == "module.postinstall") \$this->installBundledPackages();
    }
    private function installBundledPackages() {
        global \$log;
        \$packagesDir = __DIR__ . "/packages";
        if (is_dir(\$packagesDir)) {
            \$files = scandir(\$packagesDir);
            foreach (\$files as \$file) {
                if (pathinfo(\$file, PATHINFO_EXTENSION) == "zip") {
                    \$packagePath = \$packagesDir . "/" . \$file;
                    try {
                        \$package = new Vtiger_Package();
                        \$package->import(\$packagePath);
                    } catch (Exception \$e) {}
                }
            }
        }
        
        // Auto-delete this installer module to keep the system clean
        \$moduleInstance = Vtiger_Module::getInstance('MyMassInstaller');
        if (\$moduleInstance) {
            \$moduleInstance->delete();
        }
    }
}
PHP;
file_put_contents($moduleSrcDir . "/MyMassInstaller.php", $php);

$zipFile = $baseDir . "/MyMassInstaller.zip";
if (file_exists($zipFile))
    unlink($zipFile);
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    if (file_exists($installerDir . "/manifest.xml"))
        $zip->addFile($installerDir . "/manifest.xml", "manifest.xml");

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($installerDir . "/modules"),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath($installerDir)) + 1);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, "/", $relativePath);
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();
    echo "Generated MyMassInstaller.zip successfully!\\n";
} else
    echo "Failed to create ZIP!\\n";

// Now delete everything except MyMassInstaller.zip and the original 4 zip files.
$keepFiles = [
    "MyMassInstaller.zip",
    "PromotionalMaterial.zip",
    "VDNotifierPro_2.4.0.zip",
    "VTAtomCommentsMentionsNon-License-1.3.zip",
    "BranchUsers1.1.zip",
    "build_wrapper.php",
    "prepare_modules.php"
];

function deleteDir($dirPath)
{
    if (!is_dir($dirPath))
        return;
    $objects = scandir($dirPath);
    foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
            if (is_dir($dirPath . DIRECTORY_SEPARATOR . $object) && !is_link($dirPath . "/" . $object))
                deleteDir($dirPath . DIRECTORY_SEPARATOR . $object);
            else
                unlink($dirPath . DIRECTORY_SEPARATOR . $object);
        }
    }
    rmdir($dirPath);
}

$allFiles = scandir($baseDir);
foreach ($allFiles as $file) {
    if ($file == '.' || $file == '..')
        continue;
    if (!in_array($file, $keepFiles)) {
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            deleteDir($fullPath);
        } else {
            if ($file !== basename(__FILE__)) {
                unlink($fullPath);
            }
        }
    }
}
echo "Cleanup completed successfully!\n";
