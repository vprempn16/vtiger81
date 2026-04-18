<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {

		//To stop saveing the value of salutation as '--None--'
		$salutationType = $request->get('salutationtype');
		if ($salutationType === '--None--') {
			$request->set('salutationtype', '');
		}
		$this->validateHierarchyAssignedOwner($request);
		parent::process($request);
	}

	/**
	 * @param Vtiger_Request $request
	 * @return void
	 * @throws AppException
	 */
	private function validateHierarchyAssignedOwner(Vtiger_Request $request) {
		$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
		if (!file_exists($hierarchyHelper)) {
			return;
		}
		require_once $hierarchyHelper;
		$ownerRaw = $request->get('assigned_user_id');
		$ownerId = $this->normalizeUserId($ownerRaw);
		if ($ownerId <= 0) {
			return;
		}
		if (!$this->shouldValidateOwnerChange($request, $ownerId)) {
			return;
		}
		if (!BranchUsers_HierarchyAccess::isAssignmentAllowedForCurrentUser($ownerId)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * Validate only for create or when owner actually changes.
	 *
	 * @param Vtiger_Request $request
	 * @param int $ownerId
	 * @return bool
	 */
	private function shouldValidateOwnerChange(Vtiger_Request $request, $ownerId) {
		$recordId = (int)$request->get('record');
		if ($recordId <= 0) {
			return true;
		}
		$db = PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT smownerid FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",
			array($recordId)
		);
		if ($db->num_rows($r) < 1) {
			return true;
		}
		$currentOwnerId = (int)$db->query_result($r, 0, 'smownerid');
		return $currentOwnerId !== (int)$ownerId;
	}

	/**
	 * @param mixed $value
	 * @return int
	 */
	private function normalizeUserId($value) {
		$value = trim((string)$value);
		if ($value === '') {
			return 0;
		}
		$parts = explode('x', $value);
		$value = end($parts);
		return (int)$value;
	}
}
