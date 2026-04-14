<?php
/*+**********************************************************************************
 * Branch hierarchy: branch root resolution and project branch scope (vtiger_project_branch_scope).
 ************************************************************************************/

class BranchUsers_HierarchyAccess {

	const MAX_PARENT_WALK = 64;

	/**
	 * Reset cached schema introspection (e.g. after DDL in same request).
	 *
	 * @return void
	 */
	public static function resetCache() {
		self::$scopeTableExists = null;
		self::$mapHasStatusColumn = null;
		self::$mapHasPartnerColumn = null;
	}

	/** @var bool|null */
	private static $scopeTableExists = null;

	/** @var bool|null */
	private static $mapHasStatusColumn = null;

	/** @var bool|null */
	private static $mapHasPartnerColumn = null;

	/**
	 * @param PearDatabase|null $db
	 * @return bool
	 */
	public static function scopeTableExists($db = null) {
		if (self::$scopeTableExists !== null) {
			return self::$scopeTableExists;
		}
		$db = $db ?: PearDatabase::getInstance();
		$r = $db->pquery("SHOW TABLES LIKE 'vtiger_project_branch_scope'", array());
		self::$scopeTableExists = ($db->num_rows($r) > 0);
		return self::$scopeTableExists;
	}

	/**
	 * @param PearDatabase|null $db
	 * @return bool
	 */
	public static function mapHasStatusColumn($db = null) {
		if (self::$mapHasStatusColumn !== null) {
			return self::$mapHasStatusColumn;
		}
		$db = $db ?: PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vtiger_user_branch_map' AND COLUMN_NAME = 'status' LIMIT 1",
			array()
		);
		self::$mapHasStatusColumn = ($db->num_rows($r) > 0);
		return self::$mapHasStatusColumn;
	}

	/**
	 * @param PearDatabase|null $db
	 * @return bool
	 */
	public static function mapHasPartnerColumn($db = null) {
		if (self::$mapHasPartnerColumn !== null) {
			return self::$mapHasPartnerColumn;
		}
		$db = $db ?: PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vtiger_user_branch_map' AND COLUMN_NAME = 'partner_id' LIMIT 1",
			array()
		);
		self::$mapHasPartnerColumn = ($db->num_rows($r) > 0);
		return self::$mapHasPartnerColumn;
	}

	/**
	 * SQL fragment for active map rows (empty if status column is missing — treat all rows as active).
	 *
	 * @return string
	 */
	public static function mapActiveRowSqlSuffix() {
		return self::mapHasStatusColumn() ? ' AND status = 1' : '';
	}

	/**
	 * Prefer Assigned To (real user); else creator; else fallback (e.g. acting user).
	 *
	 * @param PearDatabase $db
	 * @param int $smownerid
	 * @param int $smcreatorid
	 * @param int $fallbackUserId
	 * @return int
	 */
	public static function resolveUserIdForBranchRoot($db, $smownerid, $smcreatorid, $fallbackUserId) {
		$smownerid = (int)$smownerid;
		$smcreatorid = (int)$smcreatorid;
		$fallbackUserId = (int)$fallbackUserId;
		if ($smownerid > 0) {
			$check = $db->pquery(
				"SELECT id FROM vtiger_users WHERE id = ? AND deleted = 0",
				array($smownerid)
			);
			if ($db->num_rows($check) > 0) {
				return $smownerid;
			}
		}
		if ($smcreatorid > 0) {
			$check = $db->pquery(
				"SELECT id FROM vtiger_users WHERE id = ? AND deleted = 0",
				array($smcreatorid)
			);
			if ($db->num_rows($check) > 0) {
				return $smcreatorid;
			}
		}
		return $fallbackUserId > 0 ? $fallbackUserId : 0;
	}

	/**
	 * Top of parent_user_id chain for active map rows; unmapped user is treated as root of self.
	 *
	 * @param int $userId
	 * @return int|null null only if userId invalid
	 */
	public static function getBranchRootUserId($userId) {
		$userId = (int)$userId;
		if ($userId <= 0) {
			return null;
		}
		return self::computeBranchRootByWalk($userId);
	}

	/**
	 * @param int $userId
	 * @return int
	 */
	public static function computeBranchRootByWalk($userId) {
		$db = PearDatabase::getInstance();
		$st = self::mapActiveRowSqlSuffix();
		$visited = array();
		$current = (int)$userId;
		for ($i = 0; $i < self::MAX_PARENT_WALK; $i++) {
			if (isset($visited[$current])) {
				return $current;
			}
			$visited[$current] = true;
			$r = $db->pquery(
				"SELECT parent_user_id FROM vtiger_user_branch_map WHERE user_id = ?{$st}",
				array($current)
			);
			if ($db->num_rows($r) < 1) {
				return $current;
			}
			$parent = $db->query_result($r, 0, 'parent_user_id');
			if ($parent === null || $parent === '') {
				return $current;
			}
			$parent = (int)$parent;
			if ($parent <= 0) {
				return $current;
			}
			$current = $parent;
		}
		return $current;
	}

	/**
	 * Ancestors chain for a user (includes self).
	 *
	 * @param int $userId
	 * @return int[]
	 */
	public static function getAncestorChainUserIds($userId) {
		$userId = (int)$userId;
		if ($userId <= 0) {
			return array();
		}
		$db = PearDatabase::getInstance();
		$st = self::mapActiveRowSqlSuffix();
		$visited = array();
		$chain = array();
		$current = $userId;
		for ($i = 0; $i < self::MAX_PARENT_WALK; $i++) {
			if (isset($visited[$current])) {
				break;
			}
			$visited[$current] = true;
			$chain[] = (int)$current;
			$r = $db->pquery(
				"SELECT parent_user_id FROM vtiger_user_branch_map WHERE user_id = ?{$st}",
				array($current)
			);
			if ($db->num_rows($r) < 1) {
				break;
			}
			$parent = $db->query_result($r, 0, 'parent_user_id');
			if ($parent === null || $parent === '') {
				break;
			}
			$parent = (int)$parent;
			if ($parent <= 0) {
				break;
			}
			$current = $parent;
		}
		return $chain;
	}

	/**
	 * Descendants set for a user (includes self).
	 *
	 * @param int $userId
	 * @return int[]
	 */
	public static function getDescendantUserIds($userId) {
		$userId = (int)$userId;
		if ($userId <= 0) {
			return array();
		}
		$db = PearDatabase::getInstance();
		$st = self::mapActiveRowSqlSuffix();
		$result = $db->pquery(
			"SELECT user_id, parent_user_id FROM vtiger_user_branch_map WHERE 1=1{$st}",
			array()
		);
		$childrenByParent = array();
		$n = $db->num_rows($result);
		for ($i = 0; $i < $n; $i++) {
			$uid = (int)$db->query_result($result, $i, 'user_id');
			$pid = $db->query_result($result, $i, 'parent_user_id');
			if ($pid === null || $pid === '') {
				continue;
			}
			$pid = (int)$pid;
			if (!isset($childrenByParent[$pid])) {
				$childrenByParent[$pid] = array();
			}
			$childrenByParent[$pid][] = $uid;
		}
		$queue = array($userId);
		$visited = array();
		$out = array();
		while (!empty($queue)) {
			$current = (int)array_shift($queue);
			if ($current <= 0 || isset($visited[$current])) {
				continue;
			}
			$visited[$current] = true;
			$out[] = $current;
			if (!empty($childrenByParent[$current])) {
				foreach ($childrenByParent[$current] as $child) {
					$queue[] = (int)$child;
				}
			}
		}
		return $out;
	}

	/**
	 * @param int[] $ids
	 * @return string
	 */
	private static function toIntListSql($ids) {
		$out = array();
		foreach ((array)$ids as $id) {
			$id = (int)$id;
			if ($id > 0) {
				$out[$id] = $id;
			}
		}
		if (empty($out)) {
			return '0';
		}
		return implode(',', $out);
	}

	/**
	 * Strict project line filter:
	 * - creator must be an ancestor of current user (or self)
	 * - owner must be a descendant of current user (or self)
	 *
	 * @param Users $user
	 * @param string $ownerIdSql
	 * @param string $creatorIdSql
	 * @return string
	 */
	public static function appendProjectPrivateLineSqlFragment($user, $ownerIdSql, $creatorIdSql) {
		require 'user_privileges/user_privileges_' . $user->id . '.php';
		if (!empty($is_admin) && $is_admin) {
			return '';
		}
		$viewerId = (int)$user->id;
		if ($viewerId <= 0) {
			return ' AND 1=0 ';
		}
		$ancestors = self::getAncestorChainUserIds($viewerId);
		$descendants = self::getDescendantUserIds($viewerId);
		if (empty($ancestors) || empty($descendants)) {
			return ' AND 1=0 ';
		}
		$creatorList = self::toIntListSql($ancestors);
		$ownerList = self::toIntListSql($descendants);
		$ownerIdSql = trim((string)$ownerIdSql);
		$creatorIdSql = trim((string)$creatorIdSql);
		if ($ownerIdSql === '' || $creatorIdSql === '') {
			return ' AND 1=0 ';
		}
		return " AND {$creatorIdSql} IN ({$creatorList}) AND {$ownerIdSql} IN ({$ownerList}) ";
	}

	/**
	 * @param int $ancestorUserId
	 * @param int $userId
	 * @return bool
	 */
	public static function isAncestorOrSelf($ancestorUserId, $userId) {
		$ancestorUserId = (int)$ancestorUserId;
		$userId = (int)$userId;
		if ($ancestorUserId <= 0 || $userId <= 0) {
			return false;
		}
		$ancestors = self::getAncestorChainUserIds($userId);
		return in_array($ancestorUserId, $ancestors, true);
	}

	/**
	 * @param int $viewerUserId
	 * @param int $creatorUserId
	 * @param int $ownerUserId
	 * @return bool
	 */
	public static function isViewerAllowedInProjectLine($viewerUserId, $creatorUserId, $ownerUserId) {
		$viewerUserId = (int)$viewerUserId;
		$creatorUserId = (int)$creatorUserId;
		$ownerUserId = (int)$ownerUserId;
		if ($viewerUserId <= 0 || $creatorUserId <= 0 || $ownerUserId <= 0) {
			return false;
		}
		// Project line exists only when creator is on the owner ancestor chain.
		if (!self::isAncestorOrSelf($creatorUserId, $ownerUserId)) {
			return false;
		}
		// Viewer must sit between creator and owner (inclusive).
		return self::isAncestorOrSelf($creatorUserId, $viewerUserId)
			&& self::isAncestorOrSelf($viewerUserId, $ownerUserId);
	}

	/**
	 * @param int $projectCrmId
	 * @return void
	 * @throws AppException
	 */
	public static function assertProjectInPrivateLineScope($projectCrmId) {
		$projectCrmId = (int)$projectCrmId;
		if ($projectCrmId <= 0) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}
		$currentUser = vglobal('current_user');
		$viewerId = (int)$currentUser->id;
		$db = PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT smcreatorid, smownerid FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",
			array($projectCrmId)
		);
		if ($db->num_rows($r) < 1) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$creatorId = (int)$db->query_result($r, 0, 'smcreatorid');
		$ownerId = (int)$db->query_result($r, 0, 'smownerid');
		if (!self::isViewerAllowedInProjectLine($viewerId, $creatorId, $ownerId)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * SQL AND fragment: restrict expression (project id column) to current user's branch.
	 *
	 * @param Users $user
	 * @param string $projectIdSql e.g. vtiger_project.projectid
	 * @param string $scope vtiger_crmentity scope suffix e.g. '' or '_rel'
	 * @return string
	 */
	public static function appendProjectBranchScopeSqlFragment($user, $projectIdSql, $scope = '') {
		unset($scope);
		if (!self::scopeTableExists()) {
			return '';
		}
		require 'user_privileges/user_privileges_' . $user->id . '.php';
		if (!empty($is_admin) && $is_admin) {
			return '';
		}
		$root = self::getBranchRootUserId((int)$user->id);
		if ($root === null) {
			return ' AND 1=0 ';
		}
		$root = (int)$root;
		$projectIdSql = trim($projectIdSql);
		if ($projectIdSql === '') {
			return ' AND 1=0 ';
		}
		return " AND {$projectIdSql} IN (SELECT projectid FROM vtiger_project_branch_scope WHERE branch_root_user_id = {$root}) ";
	}

	/**
	 * @param int $projectCrmId vtiger_project.projectid / crmid
	 * @return void
	 * @throws AppException
	 */
	public static function assertProjectInBranchScope($projectCrmId) {
		if (!self::scopeTableExists()) {
			return;
		}
		$projectCrmId = (int)$projectCrmId;
		if ($projectCrmId <= 0) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return;
		}
		$currentUser = vglobal('current_user');
		$root = self::getBranchRootUserId((int)$currentUser->id);
		if ($root === null) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$db = PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT 1 FROM vtiger_project_branch_scope WHERE projectid = ? AND branch_root_user_id = ?",
			array($projectCrmId, (int)$root)
		);
		if ($db->num_rows($r) < 1) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	/**
	 * DetailView defense for Project / ProjectTask / ProjectMilestone.
	 *
	 * @param int $crmid
	 * @param string $moduleName
	 * @return void
	 * @throws AppException
	 */
	public static function assertCrmentityInProjectBranchScope($crmid, $moduleName) {
		if (!self::scopeTableExists()) {
			return;
		}
		$moduleName = trim((string)$moduleName);
		if ($moduleName === 'Project') {
			self::assertProjectInBranchScope((int)$crmid);
			return;
		}
		$db = PearDatabase::getInstance();
		if ($moduleName === 'ProjectTask') {
			$r = $db->pquery(
				"SELECT pt.projectid FROM vtiger_projecttask pt INNER JOIN vtiger_crmentity ce ON ce.crmid = pt.projecttaskid AND ce.deleted = 0 WHERE pt.projecttaskid = ?",
				array((int)$crmid)
			);
		} elseif ($moduleName === 'ProjectMilestone') {
			$r = $db->pquery(
				"SELECT pm.projectid FROM vtiger_projectmilestone pm INNER JOIN vtiger_crmentity ce ON ce.crmid = pm.projectmilestoneid AND ce.deleted = 0 WHERE pm.projectmilestoneid = ?",
				array((int)$crmid)
			);
		} else {
			return;
		}
		if ($db->num_rows($r) < 1) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		$pid = $db->query_result($r, 0, 'projectid');
		if ($pid === null || $pid === '') {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		self::assertProjectInBranchScope((int)$pid);
	}

	/**
	 * Ensure scope row exists after Project save. Branch root follows Assigned To (user), else creator, else saver.
	 *
	 * @param int $projectId
	 * @param Users $actingUser
	 * @return void
	 */
	public static function upsertProjectBranchScope($projectId, $actingUser) {
		$projectId = (int)$projectId;
		if ($projectId <= 0 || !self::scopeTableExists()) {
			return;
		}
		$db = PearDatabase::getInstance();
		$r = $db->pquery(
			"SELECT smownerid, smcreatorid FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",
			array($projectId)
		);
		if ($db->num_rows($r) < 1) {
			return;
		}
		$owner = (int)$db->query_result($r, 0, 'smownerid');
		$creator = (int)$db->query_result($r, 0, 'smcreatorid');
		$focus = self::resolveUserIdForBranchRoot($db, $owner, $creator, (int)$actingUser->id);
		if ($focus <= 0) {
			$focus = (int)$actingUser->id;
		}
		$root = self::getBranchRootUserId($focus);
		if ($root === null) {
			$root = $focus;
		}
		$root = (int)$root;
		$db->pquery(
			"INSERT INTO vtiger_project_branch_scope (projectid, branch_root_user_id, createdtime, modifiedtime) VALUES (?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE branch_root_user_id = VALUES(branch_root_user_id), modifiedtime = VALUES(modifiedtime)",
			array($projectId, $root)
		);
	}

	/**
	 * Insert missing scope rows (branch from Assigned To user when valid, else creator).
	 *
	 * @param PearDatabase|null $adb
	 * @return void
	 */
	public static function backfillMissingProjectScopes($adb = null) {
		$db = $adb ?: PearDatabase::getInstance();
		if (!self::scopeTableExists($db)) {
			return;
		}
		$result = $db->pquery(
			"SELECT p.projectid, ce.smownerid, ce.smcreatorid
			 FROM vtiger_project p
			 INNER JOIN vtiger_crmentity ce ON ce.crmid = p.projectid AND ce.deleted = 0
			 LEFT JOIN vtiger_project_branch_scope s ON s.projectid = p.projectid
			 WHERE s.projectid IS NULL",
			array()
		);
		$n = $db->num_rows($result);
		for ($i = 0; $i < $n; $i++) {
			$pid = (int)$db->query_result($result, $i, 'projectid');
			$owner = (int)$db->query_result($result, $i, 'smownerid');
			$creator = (int)$db->query_result($result, $i, 'smcreatorid');
			$focus = self::resolveUserIdForBranchRoot($db, $owner, $creator, $creator);
			if ($focus <= 0) {
				$focus = $creator > 0 ? $creator : $owner;
			}
			$root = self::getBranchRootUserId($focus);
			if ($root === null || $root <= 0) {
				$root = $focus > 0 ? $focus : 0;
			}
			if ($root <= 0) {
				continue;
			}
			$db->pquery(
				"INSERT INTO vtiger_project_branch_scope (projectid, branch_root_user_id, createdtime, modifiedtime) VALUES (?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE branch_root_user_id = VALUES(branch_root_user_id), modifiedtime = VALUES(modifiedtime)",
				array($pid, (int)$root)
			);
		}
	}

	/**
	 * Recompute branch_root_user_id for every existing scope row from current crmentity owner/creator.
	 *
	 * @param PearDatabase|null $adb
	 * @return void
	 */
	public static function realignAllProjectBranchScopesFromOwners($adb = null) {
		$db = $adb ?: PearDatabase::getInstance();
		if (!self::scopeTableExists($db)) {
			return;
		}
		$result = $db->pquery(
			"SELECT s.projectid, ce.smownerid, ce.smcreatorid
			 FROM vtiger_project_branch_scope s
			 INNER JOIN vtiger_crmentity ce ON ce.crmid = s.projectid AND ce.deleted = 0",
			array()
		);
		$n = $db->num_rows($result);
		for ($i = 0; $i < $n; $i++) {
			$pid = (int)$db->query_result($result, $i, 'projectid');
			$owner = (int)$db->query_result($result, $i, 'smownerid');
			$creator = (int)$db->query_result($result, $i, 'smcreatorid');
			$focus = self::resolveUserIdForBranchRoot($db, $owner, $creator, $creator);
			if ($focus <= 0) {
				$focus = $creator > 0 ? $creator : $owner;
			}
			$root = self::getBranchRootUserId($focus);
			if ($root === null || $root <= 0) {
				$root = $focus > 0 ? $focus : 0;
			}
			if ($root <= 0) {
				continue;
			}
			$db->pquery(
				"UPDATE vtiger_project_branch_scope SET branch_root_user_id = ?, modifiedtime = NOW() WHERE projectid = ?",
				array((int)$root, $pid)
			);
		}
	}
}
