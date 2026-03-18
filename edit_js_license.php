<?php
$filepath = 'layouts/v7/modules/Settings/VTAtomCommentsMentions/resources/LicenseManager.js';

if (file_exists($filepath)) {
    $js = file_get_contents($filepath);
    
    $pos = strpos($js, 'serialize');
    if ($pos !== false) {
        echo "✅ Found 'serialize' at position $pos<br>";
        echo "Context: <pre>" . htmlspecialchars(substr($js, $pos - 40, 80)) . "</pre>";
        
        // Execute the replacement!
        $target = 'var formData = form.serializeFormData();';
        // Let's use PREG_REPLACE with space buffers to find the Exact one in context
        $regex = '/var\s+formData\s*=\s*form\.serializeFormData\(\);/s';
        $replacement = 'var formData = form.serializeFormData() || {}; formData[app.getModuleName()] = licensekey;';
        
        $js_new = preg_replace($regex, $replacement, $js);
        if (file_put_contents($filepath, $js_new)) {
             echo "✅ Replaced and Saved successfully!";
        } else {
             echo "❌ Failed to save!";
        }
    } else {
        echo "❌ Substring 'serialize' not found at all!";
    }
} else {
    echo "❌ File not found!";
}
