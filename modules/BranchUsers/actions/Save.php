<?php
/*+**********************************************************************************
 * BranchUsers Save action.
 *
 * IMPORTANT:
 * - Validates permissions in real user context.
 * - Switches to active admin ONLY for Users save.
 * - Always restores $current_user.
 ************************************************************************************/

class BranchUsers_Save_Action extends Vtiger_Action_Controller {
	public function requiresPermission(Vtiger_Request $request) {
		// Enforce permissions in checkPermission() to allow explicit admin bypass.
		return array();
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if (!$moduleModel || !$userPrivilegesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$currentUser = vglobal('current_user');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$isAdmin = ($currentUserModel && $currentUserModel->isAdminUser());

		
		require_once 'modules/BranchUsers/helpers/PartnerAccess.php';
		require_once 'modules/BranchUsers/helpers/HierarchyAccess.php';

		$recordId = (int)$request->get('record');
		if ($recordId) {
			if (!BranchUsers_PartnerAccess::canManageUser($recordId)) {
				throw new AppException('LBL_PERMISSION_DENIED');
			}
		}

		$userName = trim((string)$request->get('user_name'));
		$firstName = trim((string)$request->get('first_name'));
		$lastName = trim((string)$request->get('last_name'));
		$email1 = trim((string)$request->get('email1'));
		$status = $request->get('status');
		$status = ($status === 'Inactive') ? 'Inactive' : 'Active';
		$requestedRoleId = trim((string)$request->get('roleid'));
		$parentUserIdFromForm = (int)$request->get('parent_user_id');

		if ($userName === '' || $lastName === '') {
			throw new AppException(vtranslate('LBL_INVALID_DATA', 'Vtiger'));
		}

		$roleId = $this->resolveRoleId($currentUserModel, $requestedRoleId, $isAdmin);
		if (empty($roleId)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
		}

		// For new users, require a password at creation.
		$newPassword = (string)$request->get('new_password');
		if (!$recordId) {
			if (trim($newPassword) === '') {
				throw new AppException(vtranslate('LBL_PASSWORD', 'Users') . ' ' . vtranslate('LBL_IS_REQUIRED', 'Vtiger'));
			}
			if (!$this->isPasswordStrong($newPassword)) {
				throw new AppException(vtranslate('LBL_PASSWORDNOTSTRONG', 'Vtiger'));
			}
		}

		global $current_user;
		$backupUser = $current_user;
		$current_user = Users::getActiveAdminUser();
		try {
			if ($recordId) {
				$userRecord = Users_Record_Model::getInstanceById($recordId, 'Users');
				$userRecord->set('mode', 'edit');
			} else {
				$userRecord = Users_Record_Model::getCleanInstance('Users');
				$userRecord->set('mode', '');
				$userRecord->set('user_password', $newPassword);
				$userRecord->set('confirm_password', $newPassword);
				$userRecord->set('is_admin', 'off');
			}

			$userRecord->set('user_name', $userName);
			$userRecord->set('first_name', $firstName);
			$userRecord->set('last_name', $lastName);
			$userRecord->set('email1', $email1);
			$userRecord->set('status', $status);
			$userRecord->set('roleid', $roleId);

			$userRecord->save();
			$savedUserId = (int)$userRecord->getId();

			if ($recordId && trim($newPassword) !== '') {
				if (!$this->isPasswordStrong($newPassword)) {
					throw new AppException(vtranslate('LBL_PASSWORDNOTSTRONG', 'Vtiger'));
				}
				$user = new Users();
				$user->retrieve_entity_info($savedUserId, 'Users');
				$user->id = $savedUserId;
				$user->change_password('', $newPassword, false);
			}
		} finally {
			$current_user = $backupUser;
		}

		$creatorUserId = ($backupUser && !empty($backupUser->id)) ? (int)$backupUser->id : (int)$currentUser->id;
		
		if (!$recordId && $savedUserId > 0) {
			// Keep real creator (operator) even though save runs in admin context.
			$dbCreator = PearDatabase::getInstance();
			//$dbCreator->pquery("UPDATE vtiger_crmentity SET smcreatorid = ? WHERE crmid = ?",array($creatorUserId, (int)$savedUserId));
		}

		// Maintain mapping: tolerate deployments where partner_id column is removed.
		
		if (!$isAdmin) {
			$db = PearDatabase::getInstance();
			$hasPartnerColumn = BranchUsers_HierarchyAccess::mapHasPartnerColumn($db);
			$partnerId = BranchUsers_PartnerAccess::getUserPartner((int)$currentUser->id);
			$partnerId = ($partnerId === null) ? 0 : (int)$partnerId;
			$firstChildRoleId = $this->getFirstChildRoleId((string)$currentUserModel->get('roleid'));
			if ($firstChildRoleId !== '' && $roleId === $firstChildRoleId) {
				$parentUserId = (int)$currentUser->id;
			} else {
				$parentRoleId = $this->getImmediateParentRoleId($roleId);
				$parentUserId = $this->resolveParentUserId($parentUserIdFromForm, $parentRoleId, (int)$currentUser->id, (int)$savedUserId);
			}
			$exists = $db->pquery(
				"SELECT 1 FROM vtiger_user_branch_map WHERE user_id = ?",
				array((int)$savedUserId)
			);
			if ($db->num_rows($exists) > 0) {
				if ($hasPartnerColumn) {
					$db->pquery(
						"UPDATE vtiger_user_branch_map SET parent_user_id = ?, partner_id = ?, creatorid = ? WHERE user_id = ?",
						array((int)$parentUserId, $partnerId, $creatorUserId, (int)$savedUserId)
					);
				} else {
					$db->pquery(
						"UPDATE vtiger_user_branch_map SET parent_user_id = ?, creatorid = ? WHERE user_id = ?",
						array((int)$parentUserId, $creatorUserId, (int)$savedUserId)
					);
				}
			} else {
				if ($hasPartnerColumn) {
					$db->pquery(
						"INSERT INTO vtiger_user_branch_map (user_id, parent_user_id, partner_id, creatorid) VALUES (?, ?, ?, ?)",
						array((int)$savedUserId, (int)$parentUserId, $partnerId, $creatorUserId)
					);
				} else {
					$db->pquery(
						"INSERT INTO vtiger_user_branch_map (user_id, parent_user_id, creatorid) VALUES (?, ?, ?)",
						array((int)$savedUserId, (int)$parentUserId, $creatorUserId)
					);
				}
			}
		} else if ($parentUserIdFromForm > 0) {
			// For admin, update existing row or create missing row.
			$db = PearDatabase::getInstance();
			$hasPartnerColumn = BranchUsers_HierarchyAccess::mapHasPartnerColumn($db);
			$partnerId = BranchUsers_PartnerAccess::getUserPartner($parentUserIdFromForm);
			$partnerId = ($partnerId === null) ? 0 : (int)$partnerId;
			$exists = $db->pquery(
				"SELECT 1 FROM vtiger_user_branch_map WHERE user_id = ?",
				array((int)$savedUserId)
			);
			if ($db->num_rows($exists) > 0) {
				if ($hasPartnerColumn) {
					$db->pquery(
						"UPDATE vtiger_user_branch_map SET parent_user_id = ?, partner_id = ?, creatorid = ? WHERE user_id = ?",
						array((int)$parentUserIdFromForm, $partnerId, $creatorUserId, (int)$savedUserId)
					);
				} else {
					$db->pquery(
						"UPDATE vtiger_user_branch_map SET parent_user_id = ?, creatorid = ? WHERE user_id = ?",
						array((int)$parentUserIdFromForm, $creatorUserId, (int)$savedUserId)
					);
				}
			} else {
				if ($hasPartnerColumn) {
					$db->pquery(
						"INSERT INTO vtiger_user_branch_map (user_id, parent_user_id, partner_id, creatorid) VALUES (?, ?, ?, ?)",
						array((int)$savedUserId, (int)$parentUserIdFromForm, $partnerId, $creatorUserId)
					);
				} else {
					$db->pquery(
						"INSERT INTO vtiger_user_branch_map (user_id, parent_user_id, creatorid) VALUES (?, ?, ?)",
						array((int)$savedUserId, (int)$parentUserIdFromForm, $creatorUserId)
					);
				}
			}
		}
		$this->emitSuccessResponse($request, array('id' => $savedUserId));
	}

	private function isPasswordStrong($password) {
		$runtimeConfigs = Vtiger_Runtime_Configs::getInstance();
		$regex = $runtimeConfigs->getValidationRegex('password_regex');
		if (empty($regex)) {
			return true;
		}
		return (preg_match('/' . $regex . '/i', $password) === 1);
	}

	private function resolveRoleId($currentUserModel, $requestedRoleId, $isAdmin) {
		$currentRoleId = $currentUserModel->get('roleid');
		if ($requestedRoleId === '') {
			return '';
		}
		if ($isAdmin) {
			return $requestedRoleId;
		}

		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT parentrole FROM vtiger_role WHERE roleid = ?",
			array($currentRoleId)
		);
		if ($db->num_rows($result) < 1) {
			return '';
		}
		$currentParentRole = $db->query_result($result, 0, 'parentrole');

		$targetResult = $db->pquery(
			"SELECT parentrole FROM vtiger_role WHERE roleid = ?",
			array($requestedRoleId)
		);
		if ($db->num_rows($targetResult) < 1) {
			return '';
		}
		$targetParentRole = $db->query_result($targetResult, 0, 'parentrole');
		if ($requestedRoleId === $currentRoleId || strpos($targetParentRole, $currentParentRole . '::') === 0) {
			return $requestedRoleId;
		}
		return '';
	}

	private function getFirstChildRoleId($currentRoleId) {
		$db = PearDatabase::getInstance();
		$currentRoleResult = $db->pquery(
			"SELECT parentrole, depth FROM vtiger_role WHERE roleid = ?",
			array($currentRoleId)
		);
		if ($db->num_rows($currentRoleResult) < 1) {
			return '';
		}
		$currentParentRole = (string)$db->query_result($currentRoleResult, 0, 'parentrole');
		$currentDepth = (int)$db->query_result($currentRoleResult, 0, 'depth');

		$result = $db->pquery(
			"SELECT roleid
			 FROM vtiger_role
			 WHERE parentrole LIKE ? AND depth = ?
			 ORDER BY parentrole ASC
			 LIMIT 1",
			array($currentParentRole . '::%', $currentDepth + 1)
		);
		if ($db->num_rows($result) < 1) {
			return '';
		}
		return (string)$db->query_result($result, 0, 'roleid');
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

	private function resolveParentUserId($candidateUserId, $roleId, $fallbackUserId, $targetUserId = 0) {
		$candidateUserId = (int)$candidateUserId;
		$targetUserId = (int)$targetUserId;
		if ($candidateUserId <= 0) {
			return (int)$fallbackUserId;
		}
		if ($targetUserId > 0 && $candidateUserId === $targetUserId) {
			throw new AppException('Parent user cannot be the same as user');
		}
		if (!BranchUsers_PartnerAccess::canManageUser($candidateUserId)) {
			throw new AppException('Selected parent user is not allowed');
		}
		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT 1
			 FROM vtiger_users u
			 INNER JOIN vtiger_user2role ur ON ur.userid = u.id
			 WHERE u.id = ? AND u.deleted = 0 AND u.status = 'Active' AND ur.roleid = ?",
			array($candidateUserId, $roleId)
		);
		if ($db->num_rows($result) > 0) {
			return $candidateUserId;
		}
		throw new AppException('Selected parent user is not valid for selected parent role');
	}

	private function emitSuccessResponse(Vtiger_Request $request, array $payload) {
		$isAjax = (bool)$request->get('ajax') || (bool)$request->get('isAjax');
		if ($isAjax) {
			$response = new Vtiger_Response();
			$response->setResult($payload);
			$response->emit();
			return;
		}
		header('Location: index.php?module=BranchUsers&view=List');
	}
}

