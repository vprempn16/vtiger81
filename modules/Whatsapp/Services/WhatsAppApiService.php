<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WhatsAppApiService
{
    protected $channel;
    protected $accessToken;
    protected $phoneNumberId;
    protected $businessId;
    public $baseUrl = 'https://graph.facebook.com/v21.0';

    public function __construct($channel)
    {
        if (is_numeric($channel)) {
            $this->channel = Settings_Whatsapp_Record_Model::getInstanceById($channel, 'Settings:Whatsapp');
        } else {
            $this->channel = $channel;
        }

        if ($this->channel) {
            $this->accessToken = $this->channel->get('access_token');
            $this->phoneNumberId = $this->channel->get('phone_number_id');
            $this->businessId = $this->channel->get('business_id');
        }
    }

    /**
     * Validate WhatsApp Account credentials
     * @param array $data Input data for validation (optional)
     * @return array
     */
    public function validateAccount(array $data = array())
    {
        $businessId = $this->businessId ?: ($data['business_id'] ?? '');
        $accessToken = $this->accessToken ?: ($data['access_token'] ?? '');

        if (empty($businessId)) {
            return array(
                'success' => false,
                'message' => 'Business Id Required'
            );
        }

        $url = "{$this->baseUrl}/{$businessId}";
        $response = self::request($url, $accessToken, array('fields' => 'id'), 'GET');

        if (($response['success'] ?? false) !== true) {
            return array(
                'success' => false,
                'message' => 'Unable to connect to WhatsApp API: ' . ($response['message'] ?? 'Unknown error')
            );
        }

        if (!empty($response['response']['error'])) {
            return array(
                'success' => false,
                'message' => $response['response']['error']['message'] ?? 'Invalid WhatsApp credentials'
            );
        }

        if (($response['response']['id'] ?? null) !== $businessId) {
            return array(
                'success' => false,
                'message' => 'Business ID mismatch'
            );
        }

        return array('success' => true);
    }

    /**
     * Common CURL request function
     */
    public static function request($url, $accessToken, $payload = array(), $method = 'POST', $headers = array(), $isMultipart = false)
    {
        $ch = curl_init();
        $defaultHeaders = array(
            'Authorization: Bearer ' . $accessToken,
        );
        if (!$isMultipart) {
            $defaultHeaders[] = 'Content-Type: application/json';
        }

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
        );

        switch (strtoupper($method)) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = $isMultipart ? $payload : json_encode($payload);
                break;
            case 'PUT':
            case 'PATCH':
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                $options[CURLOPT_POSTFIELDS] = $isMultipart ? $payload : json_encode($payload);
                break;
            case 'GET':
                if (!empty($payload)) {
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($payload);
                }
                break;
            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return array(
                'success' => false,
                'message' => $error,
            );
        }

        $decoded = json_decode($response, true);
        $errorMsg = '';
        $success = ($httpCode >= 200 && $httpCode < 300);

        if (!empty($decoded['error'])) {
            $success = false;
            $errorMsg = $decoded['error']['message'] ?? 'Unknown WhatsApp API error';
        }

        if (!$success && empty($errorMsg)) {
            $errorMsg = "HTTP request failed with status code: {$httpCode}";
        }

        return array(
            'httpCode' => $httpCode,
            'success' => $success,
            'message' => $errorMsg,
            'response' => $decoded,
        );
    }

    /**
     * Fetch templates from Meta Graph API
     * @return array
     */
    public function fetchTemplates()
    {
        if (empty($this->businessId) || empty($this->accessToken)) {
            return array();
        }

        $url = "{$this->baseUrl}/{$this->businessId}/message_templates";
        $response = self::request($url, $this->accessToken, array(), 'GET');

        if ($response['success']) {
            return isset($response['response']['data']) ? $response['response']['data'] : array();
        }

        return array();
    }

    /**
     * Send Message (Placeholder for future implementation)
     */
    public function sendMessage($to, $templateName, $languageCode, $components = array())
    {
        // Implementation for sending messages via Meta API
    }
}
