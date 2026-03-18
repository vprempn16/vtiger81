<?php 
class AtomsVariant_GetLineItemVariantDetails_Action extends Vtiger_Action_Controller {
    public function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        if (!$recordId) {
            $response = new Vtiger_Response();
            $response->setResult(['success' => false, 'message' => 'No record ID found']);
            $response->emit();
            return;
        }
        $query = "SELECT lineitem_id, productid, atomsvariantid, atomsvariantinfo ,sequence_no FROM vtiger_inventoryproductrel WHERE id = ?";
        $result = $adb->pquery($query, [$recordId]);
        $variantDetails = [];
        while ($row = $adb->fetch_array($result)) {
            $id = $row['lineitem_id'];
            $sequence_no = $row['sequence_no'];
            $variantDetails[$sequence_no] = [
                'productid' => $row['productid'],
                'variantid' => $row['atomsvariantid'],
                'lineItemId' => $row['lineitem_id'],
                'variantinfo' => unserialize(base64_decode($row['atomsvariantinfo'])),
                
            ];
        }
        $response = new Vtiger_Response();
        $response->setResult([
            'success' => true,
            'variantDetails' => json_encode($variantDetails),
        ]);
        $response->emit();
    }
}
