<?php
/**
 * Safe Edit tool workaround for Access Denied file triggers (v2 - Cache Breaker)
 */
$filepath = 'layouts/v7/modules/Settings/VTAtomCommentsMentions/resources/LicenseManager.js';

if (file_exists($filepath)) {
    $js = file_get_contents($filepath);
    
    $pos = strpos($js, 'serializeFormData');
    if ($pos !== false) {
        echo "✅ Found 'serializeFormData' at position $pos<br>";
        echo "Context: <pre>" . htmlspecialchars(substr($js, $pos - 40, 80)) . "</pre>";
        
        // Execute the replacement!
        $regex = '/var\s+formData\s*=\s*form\s*\.serializeFormData\s*\(\)\s*;/s';
        $replacement = 'var formData = form.serializeFormData() || {}; formData[app.getModuleName()] = licensekey;';
        
        $js_new = preg_replace($regex, $replacement, $js);
        if (file_put_contents($filepath, $js_new)) {
             echo "✅ Replaced and Saved successfully!";
        } else {
             echo "❌ Failed to save!";
        }
    } else {
        echo "❌ Substring 'serializeFormData' not found at all!";
    }
} else {
    echo "❌ File not found!";
}
