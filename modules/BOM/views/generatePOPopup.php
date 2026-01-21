<?php 
class BOM_generatePOPopup_View extends Vtiger_Index_View {
    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $viewer = $this->getViewer($request);
        global $adb;
        $lineItems = [];
        $result = $adb->pquery("SELECT productid, quantity,sequence_no FROM vtiger_inventoryproductrel WHERE id=?", [$recordId]);
        while ($row = $adb->fetch_array($result)) {
            $productId = $row['productid'];
	    $qty = $row['quantity'];
	    $sequenceNo = $row['sequence_no'];
            $vendorRes = $adb->pquery(
                "SELECT vtiger_vendor.vendorid, vtiger_vendor.vendorname,vtiger_products.productname 
                 FROM vtiger_products 
                 LEFT JOIN vtiger_vendor ON vtiger_products.vendor_id = vtiger_vendor.vendorid 
		 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
                 WHERE vtiger_products.productid = ? AND vtiger_crmentity.deleted = 0", 
                 [$productId]
            );
            $vendorId = $adb->query_result($vendorRes, 0, 'vendorid');
            $vendorName = $adb->query_result($vendorRes, 0, 'vendorname');
	                $productName = $adb->query_result($vendorRes, 0, 'productname');
            $lineItems[] = [
		'productid' => $productId,
	 	'productname' => $productName,
		'qty'       => $qty,
		'sequence_no' => $sequenceNo,
                'vendorid'  => $vendorId,
                'vendor'    => $vendorName ?: 'Not Available Vendor',
            ];
        }
	//echo"<pre>";print_r($lineItems);die('#');
        $vendorProducts = [];
        foreach ($lineItems as $item) {
            $vendorKey = $item['vendor'];
            if (!isset($vendorProducts[$vendorKey])) {
		    //$vendorProducts[$vendorKey] = [];
		    $vendorProducts[$vendorKey] = [
			    'vendorid' => $item['vendorid'],
			    'products' => []
		    ];
            }
	    //$vendorProducts[$vendorKey][] = $item;
	    $vendorProducts[$vendorKey]['products'][] = $item;
        }
	$viewer->assign('BOM_RECORD_ID', $recordId);
        $viewer->assign('VENDOR_PRODUCTS', $vendorProducts);
        $viewer->view('GeneratePOPopup.tpl', $request->getModule());
    }
}


?>
