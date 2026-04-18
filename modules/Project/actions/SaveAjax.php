<?php
/* ***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

class Project_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('saveColor');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		} else {
			$this->validatePrivateLineAssignment($request);
			parent::process($request);
		}
	}

	/**
	 * Restrict Assigned To to the current user's private hierarchy line.
	 * Prevents posting cross-line owners via crafted requests.
	 *
	 * @param Vtiger_Request $request
	 * @return void
	 * @throws AppException
	 */
	private function validatePrivateLineAssignment(Vtiger_Request $request) {
		$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
		if (!file_exists($hierarchyHelper)) {
			return;
		}
		require_once $hierarchyHelper;
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}
		$currentUser = vglobal('current_user');
		$creatorId = (int)$currentUser->id;
		$ownerRaw = $request->get('assigned_user_id');
		$ownerId = $this->normalizeUserId($ownerRaw);
		if ($ownerId <= 0) {
			return;
		}
		if (!$this->shouldValidateOwnerChange($request, $ownerId)) {
			return;
		}
		if (!BranchUsers_HierarchyAccess::isViewerAllowedInProjectLine($creatorId, $creatorId, $ownerId)) {
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

	function saveColor(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		$color = $request->get('color');
		$status = $request->get('status');

		$db->pquery('INSERT INTO vtiger_projecttask_status_color(status,color) VALUES(?,?) ON DUPLICATE KEY UPDATE color = ?', array($status, $color, $color));
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult(true);
		$response->emit();
	}

}
