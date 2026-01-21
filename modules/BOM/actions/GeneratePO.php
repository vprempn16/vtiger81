<?php
class BOM_GeneratePO_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $bomid     = $request->get('bomid');
        $vendors   = $request->get('vendors'); // array of vendors with products

        $recordModel = Vtiger_Record_Model::getInstanceById($bomid, 'BOM');

        $subject     = $recordModel->get('subject');
        $contactId   = $recordModel->get('contact_id');
        $assigned_id = $recordModel->get('assigned_user_id');
	$subtotal = $recordModel->get('subtotal');
	$total = $recordModel->get('total'); 
        require_once 'modules/PurchaseOrder/actions/Save.php';
        $saveAction = new PurchaseOrder_Save_Action();

        $createdPOs = [];
	$i =1;
        foreach ($vendors as $vendorData) {
            $vendorId = $vendorData['vendorid'];
            $products = $vendorData['products'];
            $_REQUEST = [];
            $_REQUEST['module'] = 'PurchaseOrder';
            $_REQUEST['action'] = 'Save';
            $_REQUEST['mode']   = 'create';
            $_REQUEST['record'] = '';

            $_REQUEST['subject'] = $subject;
            $_REQUEST['contact_id'] = $contactId;
            $_REQUEST['assigned_user_id'] = $assigned_id;

            if (!empty($vendorId)) {
                $_REQUEST['vendor_id'] = $vendorId;
	    }else{
	    	$_REQUEST['vendor_id'] = '';
	    }

            // mandatory inventory fields
            $_REQUEST['currency_id'] = 1;
            $_REQUEST['conversion_rate'] = 1;
            $_REQUEST['region_id'] = 0;
            $_REQUEST['taxtype'] = 'group';

            $counter = 0;
            // add line items
	    $_REQUEST["hdnProductId{$counter}"] = '';
	    $_REQUEST["lineItemType{$counter}"] = '';
	    $_REQUEST["qty{$counter}"]          = 0;
	    $_REQUEST["listPrice{$counter}"]    = 0;  // let vtiger fill or override later
	    $_REQUEST["discount_type{$counter}"] = 'zero';
	    $_REQUEST["discount{$counter}"]      = 'on';
	    $_REQUEST["comment{$counter}"]       = 0;
	    $_REQUEST["discount_percentage{$counter}"] = '';
            $counter = 1;
            foreach ($products as $product) {
		$priceData = $this->getProductUnitPrice($product['productid'],$product['sequence'],$bomid);
		$listPrice = (float)$priceData['listprice'];
	    	$qty = (float)$product['qty'];
		//echo"<pre>";print_r([$priceData,$product['productid'],$i]);echo"</pre>";
                $_REQUEST["hdnProductId{$counter}"] = $product['productid'];
                $_REQUEST["lineItemType{$counter}"] = 'Products';
                $_REQUEST["qty{$counter}"]          = $product['qty'];
		$_REQUEST["listPrice{$counter}"]    = $priceData['listprice'];
		$_REQUEST["discount_type{$counter}"] = 'zero';
                $_REQUEST["discount{$counter}"]      = 'on';
                $_REQUEST["comment{$counter}"]       = "Generated from BOM #$bomid";
		$_REQUEST["atompricehike{$counter}"]  = $priceData['atompricehike'];
		$_REQUEST["atomsorgprice{$counter}"]   = $priceData['atomorgprice'];
	    	$subtotal += ($qty * $listPrice);

		$counter++;
		$i++;
            }

	    $_REQUEST['totalProductCount'] = $counter;
	    $_REQUEST['subtotal'] = $subtotal;
	    $_REQUEST['total']    = $subtotal;   // assuming no taxes/adjustments at this point
	    $_REQUEST['pre_tax_total'] = $subtotal;
	    $_REQUEST['balance']  = $subtotal;

           $vtRequest = new Vtiger_Request($_REQUEST);
	    
 	   $saveAction->process($vtRequest);

     	   $createdPOs[] = $saveAction->recordId;
	}
        $response = new Vtiger_Response();
        $response->setResult([
            'success' => true,
            'created_pos' => $createdPOs
        ]);
        $response->emit();
    }
    private function getProductUnitPrice($productId,$sequence_no,$bomid) {
	    global $adb;
	    $adb->setDebug(true);
	    $result = $adb->pquery("SELECT * FROM `vtiger_inventoryproductrel` WHERE `id` = ? AND `productid` = ? AND `sequence_no` = ?", [$bomid,$productId,$sequence_no]);
	    if ($adb->num_rows($result)) {
		    return [
			    'listprice'    => $adb->query_result($result, 0, 'listprice'),
			    'atompricehike'=> $adb->query_result($result, 0, 'atompricehike'),
			    'atomorgprice' => $adb->query_result($result, 0, 'atomorgprice')
		    ];
	    }
	    return [
		    'listprice' => 0, 'atompricehike'=> 0, 'atomorgprice' => 0
	    ];
    }
}

