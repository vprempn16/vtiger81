<?php
/*+**********************************************************************************
 * BranchUsers role-user picker data source.
 ************************************************************************************/

class BranchUsers_GetRoleUsers_Action extends Vtiger_Action_Controller {
	public function requiresPermission(Vtiger_Request $request) {
		return array();
	}

	public function checkPermission(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}
		$moduleName = $request->getModule();
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if (!$moduleModel || !$userPrivilegesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$isAdmin = ($currentUserModel && $currentUserModel->isAdminUser());
		require_once 'modules/BranchUsers/helpers/PartnerAccess.php';
		$roleId = trim((string)$request->get('roleid'));
		$excludeUserId = (int)$request->get('exclude_user_id');

		if ($roleId === '') {
			$response = new Vtiger_Response();
			$response->setResult(array('success' => true, 'users' => array(), 'roleLabel' => ''));
			$response->emit();
			return;
		}

		if (!$isAdmin && !$this->isRoleAllowedForCurrentUser($roleId, $currentUserModel->get('roleid'))) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}

		$db = PearDatabase::getInstance();
		// Show users for the immediate parent role of selected role.
		$fallbackToParent = false;
		$effectiveRoleId = $this->getImmediateParentRoleId($roleId);
		$roleLabel = $this->getRoleLabel($effectiveRoleId);
		$result = $this->getUsersByRole($effectiveRoleId, $excludeUserId);

		if (empty($effectiveRoleId)) {
			$roleLabel = '';
			$result = $db->pquery(
				"SELECT u.id, u.user_name, u.first_name, u.last_name, u.status
				 FROM vtiger_users u WHERE 1 = 0",
				array()
			);
		}

		$users = array();
		$count = $db->num_rows($result);
		for ($i = 0; $i < $count; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$candidateId = (int)$row['id'];
			if (!$isAdmin && !BranchUsers_PartnerAccess::canManageUser($candidateId)) {
				continue;
			}
			$fullName = trim($row['first_name'] . ' ' . $row['last_name']);
			if ($fullName === '') {
				$fullName = $row['user_name'];
			}
			$users[] = array(
				'id' => $candidateId,
				'user_name' => $row['user_name'],
				'full_name' => $fullName
			);
		}

		$response = new Vtiger_Response();
		$response->setResult(array(
			'success' => true,
			'users' => $users,
			'roleLabel' => $roleLabel,
			'effectiveRoleId' => $effectiveRoleId,
			'fallbackToParent' => $fallbackToParent
		));
		$response->emit();
	}

	private function isRoleAllowedForCurrentUser($targetRoleId, $currentRoleId) {
		$db = PearDatabase::getInstance();
		$roleResult = $db->pquery(
			"SELECT parentrole FROM vtiger_role WHERE roleid = ?",
			array($currentRoleId)
		);
		if ($db->num_rows($roleResult) < 1) {
			return false;
		}
		$currentParentRole = $db->query_result($roleResult, 0, 'parentrole');

		$targetResult = $db->pquery(
			"SELECT parentrole FROM vtiger_role WHERE roleid = ?",
			array($targetRoleId)
		);
		if ($db->num_rows($targetResult) < 1) {
			return false;
		}
		$targetParentRole = $db->query_result($targetResult, 0, 'parentrole');

		return ($targetRoleId === $currentRoleId) || (strpos($targetParentRole, $currentParentRole . '::') === 0);
	}

	private function getUsersByRole($roleId, $excludeUserId = 0) {
		$db = PearDatabase::getInstance();
		$query = "SELECT u.id, u.user_name, u.first_name, u.last_name, u.status
			 FROM vtiger_users u
			 INNER JOIN vtiger_user2role ur ON ur.userid = u.id
			 WHERE u.deleted = 0
			   AND u.status = 'Active'
			   AND ur.roleid = ?";
		$params = array($roleId);
		if ($excludeUserId > 0) {
			$query .= " AND u.id <> ?";
			$params[] = $excludeUserId;
		}
		$query .= " ORDER BY last_name ASC, first_name ASC, user_name ASC";
		return $db->pquery(
			$query,
			$params
		);
	}

	private function getRoleLabel($roleId) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT rolename FROM vtiger_role WHERE roleid = ?", array($roleId));
		if ($db->num_rows($result) < 1) {
			return '';
		}
		return (string)$db->query_result($result, 0, 'rolename');
	}

	private function getImmediateParentRoleId($roleId) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT parentrole FROM vtiger_role WHERE roleid = ?", array($roleId));
		if ($db->num_rows($result) < 1) {
			return '';
		}
		$parentRolePath = (string)$db->query_result($result, 0, 'parentrole');
		if ($parentRolePath === '') {
			return '';
		}
		$parts = explode('::', $parentRolePath);
		$count = count($parts);
		if ($count < 2) {
			return '';
		}
		return (string)$parts[$count - 2];
	}

}

