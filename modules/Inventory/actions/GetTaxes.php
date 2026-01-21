<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Inventory_GetTaxes_Action extends Vtiger_Action_Controller {

	public function requiresPermission(\Vtiger_Request $request) {
		$permissions = parent::requiresPermission($request);
		$permissions[] = array('module_parameter' => 'sourceModule', 'action' => 'DetailView');
		return $permissions;
	}
	
	function process(Vtiger_Request $request) {
		$decimalPlace = getCurrencyDecimalPlaces();
		$currencyId = $request->get('currency_id');
		$currencies = Inventory_Module_Model::getAllCurrencies();
		$conversionRate = $conversionRateForPurchaseCost = 1;

		$idList = $request->get('idlist');
		if (!$idList) {
			$recordId = $request->get('record');
			$idList = array($recordId);
		}

		$response = new Vtiger_Response();
		$namesList = $purchaseCostsList = $taxesList = $listPricesList = $listPriceValuesList = array();
		$descriptionsList = $quantitiesList = $imageSourcesList = $productIdsList = $baseCurrencyIdsList = array();

		foreach($idList as $id) {
			$recordModel = Vtiger_Record_Model::getInstanceById($id);
			$taxes = $recordModel->getTaxes();
			foreach ($taxes as $key => $taxInfo) {
				$taxInfo['compoundOn'] = json_encode($taxInfo['compoundOn']);
				$taxes[$key] = $taxInfo;
			}

			$taxesList[$id]				= $taxes;
			$namesList[$id]				= decode_html($recordModel->getName());
			$quantitiesList[$id]		= $recordModel->get('qtyinstock');
			$descriptionsList[$id]		= decode_html($recordModel->get('description'));
			$listPriceValuesList[$id]	= $recordModel->getListPriceValues($recordModel->getId());

			$priceDetails = $recordModel->getPriceDetails();
			foreach ($priceDetails as $currencyDetails) {
				if ($currencyId == $currencyDetails['curid']) {
					$conversionRate = $currencyDetails['conversionrate'];
				}
			}
			$listPricesList[$id] = (float)$recordModel->get('unit_price') * (float)$conversionRate;

			foreach ($currencies as $currencyInfo) {
				if ($currencyId == $currencyInfo['curid']) {
					$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
					break;
				}
			}
			$purchaseCostsList[$id] = round((float)$recordModel->get('purchase_cost') * (float)$conversionRateForPurchaseCost, $decimalPlace);
			$baseCurrencyIdsList[$id] = getProductBaseCurrency($id, $recordModel->getModuleName());

			if ($recordModel->getModuleName() == 'Products') {
				$productIdsList[] = $id;
			}
            $module = getSalesEntityType($id);
            if($module == 'Services'){  //ISSUE266 Start
                global $adb;
                $scQuery = "SELECT sc.consultantname, u.first_name, u.last_name, u.id as userid,sc.servicecompetencyid
                    FROM vtiger_servicecompetency sc
                    INNER JOIN vtiger_crmentity e ON e.crmid = sc.servicecompetencyid AND e.deleted = 0
                    INNER JOIN vtiger_users u ON u.id = sc.consultantname
                    WHERE sc.servicename = ?";
                $scRes = $adb->pquery($scQuery, [$id]);

                while ($scRow = $adb->fetch_array($scRes)) {
                    $consultantId   = $scRow['userid'];
                    $consultantName = trim($scRow['first_name'].' '.$scRow['last_name']);
                    $servicecompetencyid = $scRow['servicecompetencyid'];
                    $ticketQuery = "
                        SELECT COUNT(*) as ticket_count
                        FROM vtiger_troubletickets tt
                        INNER JOIN vtiger_crmentity e2 ON e2.crmid = tt.ticketid AND e2.deleted = 0
                        WHERE e2.smownerid = ?
                        AND MONTH(e2.createdtime) = ?
                        AND YEAR(e2.createdtime) = ?";
                    $ticketRes = $adb->pquery($ticketQuery, [$consultantId, $soMonth, $soYear]);
                    $ticketCount = (int)$adb->query_result($ticketRes, 0, 'ticket_count');

                    // 🔹 Skip consultants with > 22 tickets
                    $consultantsAvailable[$id][] = [
                        'id' => $consultantId,
                        'name' => $consultantName,
                        'servicecompetencyid' => $servicecompetencyid
                    ];
                }
            } 
            //ISSUE266 End
		}

		if ($productIdsList) {
			$imageDetailsList = Products_Record_Model::getProductsImageDetails($productIdsList);
			foreach ($imageDetailsList as $productId => $imageDetails) {
				$imageSourcesList[$productId] = $imageDetails[0]['path'].'_'.$imageDetails[0]['orgname'];
			}
		}

		foreach($idList as $id) {
			$resultData = array(
								'id'					=> $id,
								'name'					=> $namesList[$id],
								'taxes'					=> $taxesList[$id],
								'listprice'				=> $listPricesList[$id],
								'listpricevalues'		=> $listPriceValuesList[$id],
								'purchaseCost'			=> $purchaseCostsList[$id],
								'description'			=> $descriptionsList[$id],
								'baseCurrencyId'		=> $baseCurrencyIdsList[$id],
								'quantityInStock'		=> $quantitiesList[$id],
								'imageSource'			=> $imageSourcesList[$id],
                                'consultants_list'      => $consultantsAvailable[$id], //ISSUE266
					);

			$info[] = array($id => $resultData);
		}
		$response->setResult($info);
		$response->emit();
	}
}
