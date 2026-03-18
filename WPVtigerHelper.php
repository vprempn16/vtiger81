<?php
if (!file_exists("vendor/autoload.php")) {
    echo "Please install composer dependencies.";
    exit;
}

//Overrides GetRelatedList : used to get related query
//TODO : Eliminate below hacking solution
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
class WPVtigerHelper extends CRMEntity {
    private $apiToken;
    private $apiUrl;

    public function __construct(){
        $this->apiToken = 'ipvj8059ahu1hy9bs5b3y3cfgsrmio07';
        $this->apiUrl = 'https://deveg.beesline.com/rest/V1/orders?searchCriteria[pageSize]=10&searchCriteria[currentPage]=1&searchCriteria[sortOrders][0][field]=created_at&searchCriteria[sortOrders][0][direction]=DESC';

        $orders = $this->getOrders();

        foreach ($orders as $order) {
            $this->createInvoiceFromOrder($order);
        }
    }

    private function callApi($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('API Error: ' . curl_error($ch));
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    private function getOrders()
    {
        $response = $this->callApi($this->apiUrl);
        return isset($response['items']) ? $response['items'] : [];
    }

    private function getOrCreateVtigerRecord($module, $wpId, $fields)
    {
        global $adb;	
	if($wpId != ''){
        	$result = $adb->pquery("SELECT crmid FROM wordpress_vtiger_relation WHERE wp_id = ?", [$wpId]);
        	if ($adb->num_rows($result)) {
            		return $adb->query_result($result, 0, 'crmid');
        	}
	}
        $crmid = $this->createRecord($module, $fields);

        $adb->pquery("INSERT INTO wordpress_vtiger_relation (crmid, wp_id) VALUES (?, ?)", [$crmid, $wpId]);

        return $crmid;
    }

    private function createRecord($module, $data)
    {
        $recordModel = Vtiger_Record_Model::getCleanInstance($module);
        foreach ($data as $field => $value) {
            $recordModel->set($field, $value);
        }
        $recordModel->set('mode', '');
        $recordModel->save();
        return $recordModel->getId();
    }

    private function createInvoiceFromOrder($order){
	    global $current_user,$adb;
	    $adb->setDebug(true);
        $wpContactId = $order['customer_id'];
        $wpOrderId = $order['entity_id'];
        $contactName = $order['customer_firstname'] . ' ' . $order['customer_lastname'];
	$current = Users_Record_Model::getCurrentUserModel()->getId();

	echo"<pre>";print_r([$recordModel]);die('#');
        $contactId = $this->getOrCreateVtigerRecord('Contacts', $wpContactId, [
            'firstname' => $order['customer_firstname'],
            'lastname' => $order['customer_lastname'],
            'email' => $order['customer_email'],
	    'assigned_user_id' => 1,
        ]);

        $productLineItems = [];
        foreach ($order['items'] as $item) {
            $wpProductId = $item['product_id'];
            $productId = $this->getOrCreateVtigerRecord('Products', $wpProductId, [
                'productname' => $item['name'],
                'unit_price' => $item['price']
            ]);

            $productLineItems[] = [
                'productid' => $productId,
                'quantity' => $item['qty_ordered'],
                'listprice' => $item['price'],
            ];
        }

        $invoiceId = $this->createRecord('Invoice', [
            'subject' => 'WP Order #' . $wpOrderId,
            'contact_id' => $contactId,
            'invoicestatus' => 'Created',
            'bill_street' => $order['billing_address']['street'] ?? '',
            'bill_city' => $order['billing_address']['city'] ?? '',
            'bill_code' => $order['billing_address']['postcode'] ?? '',
            'bill_country' => $order['billing_address']['country_id'] ?? '',
        ]);

        $this->addLineItems($invoiceId, $productLineItems);

        $this->recordRelation($wpOrderId, $invoiceId);
    }

    private function addLineItems($invoiceId, $lineItems)
    {
        global $adb;

        foreach ($lineItems as $index => $item) {
            $adb->pquery("INSERT INTO vtiger_inventoryproductrel (id, productid, sequence_no, quantity, listprice) VALUES (?, ?, ?, ?, ?)", [
                $invoiceId,
                $item['productid'],
                $index + 1,
                $item['quantity'],
                $item['listprice']
            ]);
        }
    }

    private function recordRelation($wpId, $vtigerId)
    {
        global $adb;
        $adb->pquery("INSERT INTO wordpress_vtiger_relation (crmid, wp_id) VALUES (?, ?)", [$vtigerId, $wpId]);
    }
}
$cal = new WPVtigerHelper();
