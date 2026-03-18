<?php
class WhatsappHelper
{
    private $api_version;
    private $phone_number_id;
    private $business_id;
    private $access_token;
    private $base_url;
    public function __construct(){
	$this->api_version     = 'v22.0';
        //$this->phone_number_id = '701258376401177';
        //$this->business_id     = '754747687233405';
	$config = $this->getRecordDetails();
	$access_token =  Vtiger_Functions::fromProtectedText($config['access_token']);
        $this->base_url        = "https://graph.facebook.com/{$this->api_version}/";
    	$this->phone_number_id = $config['phone_number_id'];
	$this->business_id     = $config['business_id'];
	$this->access_token  = $access_token;
    }
    public function getTemplates(){
	    $list = [];
	    $url = "{$this->base_url}{$this->business_id}/message_templates";
	    $templates = $this->makeRequest($url, [], "GET");

	    if (!empty($templates['data'])) {
		    foreach ($templates['data'] as $tpl) {
			    $list[] = [
				    'id'     => $tpl['id'],
				    'name'   => $tpl['name'],
				    'status' => $tpl['status'] ?? '',
			    ];
		    }
	    }
	    return $list;
    }
    public function getTemplateById($templateId){
        $url = "{$this->base_url}{$templateId}";
        return $this->makeRequest($url, [], "GET");
    }
    public function getRecordDetails($id=""){
	    global $adb;
	    $columns = Array("app_id","app_secret","phone_number_id","business_id","access_token","created_at");
	    $retun = Array();
	    $result = $adb->pquery("SELECT * FROM atom_whatsapp_config",array());
	    $num_rows = $adb->num_rows($result);
	    if($num_rows > 0){
		    $return['id'] = $id;
		    foreach($columns as $column){
			    $return[$column] = $adb->query_result($result,0,$column);
		    }
	    }
	    return $return;
    }
    public function refreshToken(){
	    global $adb;
	    $config = $this->getRecordDetails();
	    $appId = $config['app_id'];
	    $appSecret = $config['app_secret'];
	    $shortLivedTok = $config['access_token'];
	    $shortLivedTok = Vtiger_Functions::fromProtectedText($shortLivedTok);
	    $createdAt = $config['created_at'];
	    $createdTime = new DateTime($createdAt);
	    $currentTime = new DateTime();
	    $diffMinutes = ($currentTime->getTimestamp() - $createdTime->getTimestamp()) / 60;
	    if($diffMinutes < 50){
		    // Not yet 50 mins, no need to refresh
		    return;
	    }

	    $url = "https://graph.facebook.com/v22.0/oauth/access_token"
		    . "?grant_type=fb_exchange_token"
		    . "&client_id={$appId}"
		    . "&client_secret={$appSecret}"
		    . "&fb_exchange_token={$shortLivedTok}";

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $response = curl_exec($ch);
	    if (curl_errno($ch)) {
		    echo "cURL error: " . curl_error($ch);
		    exit;
	    }
	    curl_close($ch);
	    $data = json_decode($response, true);
	    if (isset($data['access_token'])) {
		    $longLivedToken = $data['access_token'];
		    $adb->pquery("UPDATE atom_whatsapp_config SET access_token = ? ,created_at = NOW() WHERE app_id = ?",array($longLivedToken,$appId));
	    }    
    }
    public function sendMessageUsingName($recipient, $template_name, $language = "en_US", $components = []) {
	    $url = "{$this->base_url}{$this->phone_number_id}/messages";

	    $data = [
		    "messaging_product" => "whatsapp",
		    "recipient_type"    => "individual",
		    "to"                => $recipient,
		    "type"              => "template",
		    "template"          => [
			    "name"      => $template_name,
			    "language"  => ["code" => $language],
			    "components"=> $components,
		    ]
	    ];
	    return $this->makeRequest($url, $data);
    }
    private function makeRequest($url, $data, $method = "POST"){
        $headers = [
            "Authorization: Bearer {$this->access_token}",
            "Content-Type: application/json"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ($method === "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($http_code == 200) ? json_decode($response, true) : ["error" => $response];
    }
    public function isWhatsAppOptedIn($crmid, $phone) {
	global $adb;

	    $res = $adb->pquery(
		    "SELECT optin_status
		    FROM sc_whatsapp_optin
		    WHERE crmid = ? AND phone = ?
		    ORDER BY optin_time DESC
		    LIMIT 1",
	[$crmid, $phone]
	    );

	if ($adb->num_rows($res) === 0) {
		return false; // no opt-in record
	}

	return ($adb->query_result($res, 0, 'optin_status') === 'YES');
    }

}

