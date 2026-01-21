<?php          
class BOM_GetLineItemDetails_Action extends Vtiger_Action_Controller {      
	public function process(Vtiger_Request $request) {     
		global $adb;  
		$moduleName = $request->get('module');
		$recordId = $request->get('record');   
		$currentModel = Vtiger_Record_Model::getInstanceById($recordId);
		if (!$recordId) {
			$response = new Vtiger_Response();
			$response->setResult(['success' => false, 'message' => 'No record ID found']);
			$response->emit();
			return;
		}	
		$qty_multiple = $currentModel->get('qty_multiple');
		$itemDetails['qty_multiple'] = $qty_multiple;
		$response = new Vtiger_Response();
		$response->setResult([
			'success' => true,
			'itemDetails' => json_encode($itemDetails),
		]);
		$response->emit();
	
	}

}
