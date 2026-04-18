<?php
/*+**********************************************************************************
 * BranchUsers partner scope + user management checks.
 *
 * IMPORTANT:
 * - Never swaps to admin context here.
 * - All permission validation and listing must happen under the real $current_user.
 ************************************************************************************/

class BranchUsers_PartnerAccess {
	/**
	 * Get partner_id for a user (active mapping only).
	 *
	 * @param int $userId
	 * @return int|null
	 */
	public static function getUserPartner($userId) {
		require_once dirname(__FILE__) . '/HierarchyAccess.php';
		if (!BranchUsers_HierarchyAccess::mapHasPartnerColumn()) {
			return null;
		}
		$sfx = BranchUsers_HierarchyAccess::mapActiveRowSqlSuffix();
		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT partner_id FROM vtiger_user_branch_map WHERE user_id = ?{$sfx}",
			array((int)$userId)
		);
		if ($db->num_rows($result) < 1) {
			return null;
		}
		$value = $db->query_result($result, 0, 'partner_id');
		if ($value === null || $value === '') {
			return null;
		}
		return (int)$value;
	}

	/**
	 * Determine if current user can manage the target user.
	 *
	 * Rules:
	 * - Only within same partner_id OR direct parent_user_id relationship.
	 * - Never allow managing admin/account-owner users.
	 *
	 * @param int $targetUserId
	 * @return bool
	 */
	public static function canManageUser($targetUserId) {
		$currentUser = vglobal('current_user');
		$currentUserId = (int)$currentUser->id;
		$targetUserId = (int)$targetUserId;

		if ($targetUserId <= 0) {
			return false;
		}

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$isAdmin = ($currentUserModel && $currentUserModel->isAdminUser());

		$targetModel = Users_Record_Model::getInstanceById($targetUserId, 'Users');
		if (!$targetModel) {
			return false;
		}
		// Admins have global management scope.
		if ($isAdmin) {
			return true;
		}
		// Non-admin users must never manage account owner users.
		if ($targetModel->isAccountOwner()) {
			return false;
		}
		// Non-admin can always manage self in this module flow.
		if ($targetUserId === $currentUserId) {
			return true;
		}

		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT ubm.parent_user_id, ce.smcreatorid
			 FROM vtiger_users u
			 LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
			 LEFT JOIN vtiger_crmentity ce ON ce.crmid = u.id
			 WHERE u.id = ? AND u.deleted = 0",
			array($targetUserId)
		);
		if ($db->num_rows($result) < 1) {
			return false;
		}
		$row = $db->fetchByAssoc($result, 0);
		$targetParent = isset($row['parent_user_id']) && $row['parent_user_id'] !== null ? (int)$row['parent_user_id'] : null;
		$targetCreator = isset($row['smcreatorid']) ? (int)$row['smcreatorid'] : 0;

		if ($targetParent !== null && $targetParent === $currentUserId) {
			return true;
		}
		if ($targetCreator > 0 && $targetCreator === $currentUserId) {
			return true;
		}
		return false;
	}

	/**
	 * Return SQL fragment to enforce partner scope by crmentity owner.
	 *
	 * This is intended to be appended to the access-control query fragment returned by
	 * CRMEntity::getNonAdminAccessControlQuery().
	 *
	 * @param string $moduleName
	 * @param Users $user
	 * @param string $scope
	 * @return string
	 */
	public static function appendPartnerCrmentityFilter($moduleName, $user, $scope = '') {
		require('user_privileges/user_privileges_' . $user->id . '.php');
		if (!empty($is_admin) && $is_admin) {
			return '';
		}

		$partnerId = self::getUserPartner((int)$user->id);
		if ($partnerId === null) {
			// If partner mapping is unavailable/unmapped, keep native ACL behavior.
			return '';
		}

		$partnerId = (int)$partnerId;
		$scope = (string)$scope;
		require_once dirname(__FILE__) . '/HierarchyAccess.php';
		$sfx = BranchUsers_HierarchyAccess::mapActiveRowSqlSuffix();

		return " AND vtiger_crmentity{$scope}.smownerid IN (SELECT user_id FROM vtiger_user_branch_map WHERE partner_id = {$partnerId}{$sfx}) ";
	}

	/**
	 * Assert a record is within the current user's partner scope.
	 * This is defense-in-depth for direct record access (DetailView / EditView).
	 *
	 * @param int $crmid
	 * @return void
	 * @throws AppException
	 */
	public static function assertRecordInPartnerScope($crmid) {
		$currentUser = vglobal('current_user');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}

		$partnerId = self::getUserPartner((int)$currentUser->id);
		if ($partnerId === null) {
			// If partner mapping is unavailable, skip partner-level hard-fail.
			return;
		}

		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT smownerid FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",
			array((int)$crmid)
		);
		if ($db->num_rows($result) < 1) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$ownerId = (int)$db->query_result($result, 0, 'smownerid');

		require_once dirname(__FILE__) . '/HierarchyAccess.php';
		$sfx = BranchUsers_HierarchyAccess::mapActiveRowSqlSuffix();
		$allowed = $db->pquery(
			"SELECT 1 FROM vtiger_user_branch_map WHERE user_id = ? AND partner_id = ?{$sfx}",
			array($ownerId, (int)$partnerId)
		);
		if ($db->num_rows($allowed) < 1) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}
}

