<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

// 1. Core Includes
require_once 'include/main/WebUI.php';
require_once 'modules/Whatsapp/handlers/WebhookHandler.php';

$verifyToken = 'atompen_wa_verify_token';

// 2. Handle Verification (Initial Handshake)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $logFile = 'storage/whatsapp_webhook_verify.log';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Incoming GET: " . json_encode($_GET) . "\n", FILE_APPEND);

    if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $verifyToken) {
        if (ob_get_level() > 0) ob_clean(); // Clear any buffered warnings/notices
        echo $_GET['hub_challenge'];
        exit;
    }
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Verification Failed. Token Mismatch or Missing.\n", FILE_APPEND);
    http_response_code(403);
    echo "Verification failure";
    exit;
}

// 3. Handle POST (Incoming Data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawPayload = file_get_contents('php://input');
    $payload = json_decode($rawPayload, true);

    if (json_last_error() === JSON_ERROR_NONE && !empty($payload)) {
        // Log raw payload for debugging (optional/configurable)
        // file_put_contents('storage/whatsapp_webhook_raw.log', date('Y-m-d H:i:s') . ": " . $rawPayload . "\n", FILE_APPEND);

        $handler = new Whatsapp_WebhookHandler_Model();
        $handler->handle($payload);
        
        http_response_code(200);
        echo "Webhook processed successfully";
        exit;
    }
    
    http_response_code(400);
    echo "Invalid payload";
    exit;
}

http_response_code(405);
echo "Method Not Allowed";
exit;
