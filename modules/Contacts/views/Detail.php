<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Detail_View extends Accounts_Detail_View {

	function __construct() {
		parent::__construct();
	}

	public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		$recordId = (int)$request->get('record');
		if ($recordId > 0) {
			$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
			if (file_exists($hierarchyHelper)) {
				require_once $hierarchyHelper;
				BranchUsers_HierarchyAccess::assertCrmRecordInPrivateLineScope($recordId);
			}
		}
		return true;
	}

	public function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		// Getting model to reuse it in parent 
		if (!$this->record) {
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$viewer = $this->getViewer($request);
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		return parent::showModuleDetailView($request);
	}
}
