<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('include/simplehtmldom/simple_html_dom.php');

class VTWhatsappTask extends VTTask {
	public $executeImmediately =true;
	public function getFieldNames(){
		return  array();
	}

	public function doTask($entity){
		global $current_user,$adb;
		$util = new VTWorkflowUtils();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$admin = $util->adminUser();
		$module = $entity->getModuleName();
		$taskContent = Zend_Json::decode($this->getContents($entity));
		$relatedInfo = Zend_Json::decode($this->getRelatedInfo()); 

		$templateId  = $this->templateid;
		$recipientField = $this->recepients;
		require_once 'modules/WhatsappIntergration/WhatsappHelper.php';
		$helper = new WhatsappHelper();	
	 	$recordId    = $entity->getId();

		$apiData = $helper->getTemplateById($templateId);
		if (empty($apiData['components'])) {
			return; // no template
		}
		$recipient = $entity->get($recipientField);

		if (strpos($recipient, '+91') !== 0) {
		 	$recipient = '+91' . ltrim($recipient, '+'); // add prefix but avoid double +
		}

		$components = [];

		foreach ($apiData['components'] as $comp) {
			if (strtoupper($comp['type']) !== 'HEADER') {
				continue;
			}
			$format = strtoupper($comp['format'] ?? 'TEXT');
			// HEADER IMAGE
			if ($format === 'IMAGE') {
				$imageUrl = $comp['example']['header_handle'][0] ?? null;
				if ($imageUrl) {
					$components[] = [
						"type" => "header",
						"parameters" => [
							[
								"type"  => "image",
								"image" => [
									"link" => $imageUrl
								]
							]
						]
					];
				}
			}
			// HEADER TEXT
			elseif ($format === 'TEXT') {
				$headerParams = [];
				foreach ($taskContent['wa_mapping']['header'] as $idx => $fieldName) {
					$value = $entity->get($fieldName);
					if ($value === null || $value === '') {
						continue;
					}
					$param = [
						"type" => "text",
						"text" => $value
					];
					// NAMED support
					if (($apiData['parameter_format'] ?? '') === 'NAMED') {
						$param['parameter_name'] = $idx;
					}
					$headerParams[] = $param;
				}
				if (!empty($headerParams)) {
					$components[] = [
						"type"       => "header",
						"parameters" => $headerParams
					];
				}
			}
			break; // only one header allowed
		}
			
		if (!empty($taskContent['wa_mapping']['body'])) {
			$bodyParams = [];
			$resolvedParams = [];
			foreach ($taskContent['wa_mapping']['body'] as $paramName => $fieldName) {
				$value = $entity->get($fieldName);
				if ($value === null || $value === '') {
					continue;
				}

				$resolvedParams[$paramName] = $value;
				$param = [
					"type" => "text",
					"text" => $value
				];
				// ✅ name ONLY here
				if (($apiData['parameter_format'] ?? '') === 'NAMED') {
					$param['parameter_name'] = $paramName;
				}

				$bodyParams[] = $param;
			}

			if (!empty($bodyParams)) {
				$components[] = [
					"type" => "body",
					"parameters" => $bodyParams,
				];
			}
		}
		if (!empty($taskContent['wa_mapping']['button'])) {
			foreach ($apiData['components'] as $comp) {
				if (strtoupper($comp['type']) !== 'BUTTONS' || empty($comp['buttons'])) {
					continue;
				}
				foreach ($comp['buttons'] as $idx => $btn) {
					if (($btn['type'] ?? '') !== 'URL' || empty($btn['url'])) {
						continue;
					}

					if (empty($btn['example']) || empty($btn['example'][0])) {
						// static URL button → send WITHOUT parameters
						$components[] = [
							"type"     => "button",
							"sub_type" => "url",
							"index"    => (string)$idx
						];
						continue;
					}

					$url = urldecode($btn['url']);

					preg_match_all('/\{\{(\d+)\}\}/', $url, $numMatches);

					$fieldName = $taskContent['wa_mapping']['button'][$idx] ?? null;
					if (!$fieldName) {
						continue;
					}

					$value = $entity->get($fieldName);
					if ($value === null || $value === '') {
						continue;
					}

					$components[] = [
						"type"       => "button",
						"sub_type"   => "url",
						"index"      => (string)$idx,
						"parameters" => [
							[
								"type" => "text",
								"text" => $value
							]
						]
					];
				}
			}
		}
		$response = '';
		$response = $helper->sendMessageUsingName($recipient, $apiData['name'], $apiData['language'] ?? "en_US", $components);
		$status = (isset($response['error']) && !empty($response['error'])) ? 'failed' : 'sent';
		echo"<pre>";print_r([$apiData,$components,$response,$taskContent]);

		$recordModel = Vtiger_Record_Model::getCleanInstance('WhatsappIntergration');
		$recordModel->set('title', $apiData['name']);
		$recordModel->set('wapaid', $templateId);
		$recordModel->set('crmid', $recordId);
		$recordModel->set('type', 'outgoing');
		$recordModel->set('status', $status);
		$recordModel->set('content', Zend_Json::encode($components));
		$recordModel->set('media', null);
		$recordModel->set('media_type', null);
		$recordModel->set('datetime', date('Y-m-d H:i:s'));
		
		// Handle error response
		if (isset($response['error']) && !empty($response['error'])) {
			$errorValue = is_string($response['error']) ? $response['error'] : Zend_Json::encode($response['error']);
			$dataMsg = '';
			
			// Extract readable error message
			if (is_string($response['error'])) {
				$decoded = @json_decode($response['error'], true);
				if ($decoded && isset($decoded['error']['message'])) {
					$dataMsg = $decoded['error']['message'];
					if (isset($decoded['error']['error_data']['details'])) {
						$dataMsg .= ' - ' . $decoded['error']['error_data']['details'];
					}
				} else {
					$dataMsg = $response['error'];
				}
			} else {
				if (isset($response['error']['message'])) {
					$dataMsg = $response['error']['message'];
				}
				if (isset($response['error']['error_data']['details'])) {
					$dataMsg .= ' - ' . $response['error']['error_data']['details'];
				}
			}
			
			$recordModel->set('error', $errorValue);
			$recordModel->set('data', $dataMsg);
		} else {
			$recordModel->set('error', null);
			$recordModel->set('data', 'Message sent successfully');
		}
		global $adb;
		$adb->setDebug(true);
		$recordModel->save();
		echo "<pre>";
		print_r( $recordModel );
		die;
	}
	public function getContents($entity){
		$taskContents = [
			'wa_placeholders' => isset($this->content['wa_placeholders']) 
			? $this->content['wa_placeholders'] 
			: [],
			'wa_mapping' => isset($this->content['wa_mapping']) 
			? $this->content['wa_mapping'] 
			: [],
		];
		return  Zend_Json::encode($taskContents);
	}	
}
