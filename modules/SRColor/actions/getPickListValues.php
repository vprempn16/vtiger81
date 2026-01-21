<?php 
class SRColor_getPickListValues_Action extends Vtiger_Action_Controller {


        public function process(Vtiger_Request $request) {
		global $adb , $current_user ;
		$response = new Vtiger_Response();
		$return = '';
		$success = false;
		$currentModule = $request->get('current_module');
		if($currentModule != ''){
			$sql = $adb->pquery("SELECT * FROM sr_color_configuration WHERE meta_key LIKE '%{$currentModule}_%' AND status = 1",array());
			if($adb->num_rows($sql) > 0){
				$j = 1;
				for($i=0;$i<$adb->num_rows($sql);$i++){
					$metavalue[$i] = $adb->query_result($sql,$i,'meta_value');
					$metaVal = unserialize(base64_decode($metavalue[$i]));
					$picklistvalues = json_decode($metaVal['picklistvalues'],true);
				 	$meta_key =  $adb->query_result($sql,$i,'meta_key');
					list($selectedmodule,$field) = explode('_', $meta_key, 2);
					$fields[$j] = $field;
					foreach($metaVal as $key => $value){
						if (strpos($key, '_') !== false) {
							$newKey = str_replace('_', ' ', $key);
							$updatedMetaVal[$newKey] = $value;
						} else {
							$updatedMetaVal[$key] = $value;
						}
					}

					foreach($picklistvalues as $name => $label){
						$result[$field][$name] = $updatedMetaVal[$label];
					}
					$j++;
				}
				$success = true;
			}
		}
		$response->setResult(array('success'=>$success,'details'=>$result,'meta_value'=>$meta_value));
                $response->emit();
	}
}

?>
