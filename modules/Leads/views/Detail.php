<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Detail_View extends Accounts_Detail_View {

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
}
