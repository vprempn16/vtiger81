<?php 
class BOMHandler{
	function handleEvent($eventName, $entityData) {
		global $adb;
		$recordid = $entityData->getId();
		if ($eventName == 'vtiger.entity.aftersave') {
			$moduleName = $entityData->getModuleName();
			$requestData = $_REQUEST;
			if (in_array($moduleName, ['BOM'])) {
				$qty_multiple = $requestData['qty_multiple'];
				if($qty_multiple !== '' && $moduleName == 'BOM'){
					$query = "UPDATE vtiger_bom SET qty_multiple = ? WHERE bomid = ?";
					$params = [$qty_multiple, $recordid];
					$adb->pquery($query, $params);
				}
			}
		}
	}

} 




?>
