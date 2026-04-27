<?php
$zips = ["PromotionalMaterial.zip", "VDNotifierPro_2.4.0.zip", "VTAtomCommentsMentionsNon-License-1.3.zip", "BranchUsers1.1.zip"];
$targetDir = __DIR__;

foreach ($zips as $zipFile) {
    if (!file_exists($targetDir . '/' . $zipFile)) {
        echo "Missing file: $zipFile\n";
        continue;
    }
    
    // Step 1: Unzip
    $zip = new ZipArchive;
    $res = $zip->open($targetDir . '/' . $zipFile);
    $extractPath = $targetDir . '/extracted_' . basename($zipFile, '.zip');
    if (!is_dir($extractPath)) {
        mkdir($extractPath);
    }
    if ($res === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        echo "Extracted $zipFile\n";
    } else {
        echo "Failed to extract $zipFile\n";
        continue;
    }
    
    // Step 2: Modify manifest.xml
    $manifestPath = $extractPath . '/manifest.xml';
    if (file_exists($manifestPath)) {
        $xml = simplexml_load_file($manifestPath);
        if ($xml === false) {
            echo "Failed parsing XML for $zipFile\n";
            continue;
        }
        
        // Find dependencies > vtiger_version
        if (!isset($xml->dependencies)) {
            $xml->addChild('dependencies');
        }
        if (!isset($xml->dependencies->vtiger_version)) {
            $xml->dependencies->addChild('vtiger_version', '8.0.0');
        } else {
            $xml->dependencies->vtiger_version = '8.0.0';
        }
        
        $xml->asXML($manifestPath);
        echo "Updated manifest for $zipFile\n";
    } else {
        echo "manifest.xml not found in $zipFile\n";
    }
    
    // Step 3: Re-zip
    $newZipPath = $targetDir . '/ready_' . $zipFile;
    if (file_exists($newZipPath)) unlink($newZipPath);
    
    $newZip = new ZipArchive();
    $newZip->open($newZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($extractPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip hidden files ending in '.' or '..'
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            // Need to change backslashes to forward slashes for relative path formatting in ZIP
            $relativePath = substr($filePath, strlen(realpath($extractPath)) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);
            $newZip->addFile($filePath, $relativePath);
        }
    }
    $newZip->close();
    echo "Re-zipped to $newZipPath\n";
}

echo "All modules processed.\n";

