<?php 
class WhatsappIntergration_GetTemplate_View extends Vtiger_Basic_View {
	public function process(Vtiger_Request $request)	{
		$templateId   = $request->get('template_id');
		$sourceModule = $request->get('source_module');
		$contents = $request->get('contents');
		require_once 'modules/WhatsappIntergration/WhatsappHelper.php';
		$helper       = new WhatsappHelper();
		$apiData      = $helper->getTemplateById($templateId); 
		$placeholders = [];
		$selected_fields = [];
		$waRecorModel = new WhatsappIntergration_Record_Model();	
		$headerPlaceholders = $waRecorModel->getHeaderPlaceHolders($apiData);
		$bodyPlaceholders   = $waRecorModel->getBodyPlaceHolders($apiData);
		$buttonPlaceholders = $waRecorModel->getButtonPlaceHolders($apiData);

		if (empty($sourceModule)) {
			$sourceModule = 'Contacts'; // safe fallback
		}
		$fieldsForSelect = [];
		try {
			//$moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
			$recordModel = Vtiger_Record_Model::getCleanInstance($sourceModule);
			$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
			$structuredValues = $recordStrucure->getStructure();
			$moduleModel = $recordModel->getModule();
			$moduleFields = $moduleModel->getFields();
			if ($moduleModel) {
				foreach ($moduleModel->getFields() as $fieldModel) {
					$fieldsForSelect[] = [
						'name'  => $fieldModel->getName(),
						'label' => vtranslate($fieldModel->get('label'), $sourceModule)
					];
				}
				foreach ($moduleModel->getFieldsByType('phone') as $fieldModel) {
					$fieldsForRecepient[] = [
						'name'  => $fieldModel->getName(),
						'label' => vtranslate($fieldModel->get('label'), $sourceModule)
					];
				}
			}

		} catch (Exception $e) {
		}

		//echo"<pre>";print_r($moduleModel->getFieldsByType('phone'));die('#');
		if(!empty($contents)){
			$selected_fields = $contents['wa_mapping'];
			$recepients = $request->get('recepients');
		}
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD_STRUCTURE', $recordStrucure);
		$viewer->assign('SOURCE_MODULE', $sourceModule);
		$viewer->assign('PLACEHOLDERS', $placeholders);
		$viewer->assign('HEADER_PLACEHOLDERS', $headerPlaceholders);
		$viewer->assign('BODY_PLACEHOLDERS', $bodyPlaceholders);
		$viewer->assign('BUTTON_PLACEHOLDERS', $buttonPlaceholders);
		$viewer->assign('MODULE_FIELDS', $fieldsForSelect);
		$viewer->assign('RECEPIENT_FIELD', $fieldsForRecepient);
		$viewer->assign('TEMPLATE_ID',$templateId);
		$viewer->assign('SELECTED_FIELD',$selected_fields);
		$viewer->assign('RECEPIENT',$recepients);
		$viewer->assign('TEMPLATE_COMPONENTS', $apiData['components']);
		$html = $viewer->fetch('layouts/v7/modules/WhatsappIntergration/Tasks/VTWhatsappMapping.tpl');
		$response = new Vtiger_Response();
		$response->setResult(['html' => $html]);
		$response->emit();
	}
}

?>
