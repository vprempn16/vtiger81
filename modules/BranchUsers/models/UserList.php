<?php
/*+**********************************************************************************
 * BranchUsers user listing model (non-admin context only).
 ************************************************************************************/

class BranchUsers_UserList_Model {
	/**
	 * Returns list of users the current user can manage.
	 *
	 * @param int $currentUserId
	 * @return array<int, array<string, mixed>>
	 */
	public static function getManageableUsers($currentUserId, $isAdmin = false, $options = array()) {
		$db = PearDatabase::getInstance();
		$search = isset($options['search']) ? trim((string)$options['search']) : '';
		$orderBy = isset($options['orderby']) ? (string)$options['orderby'] : 'last_name';
		$sortOrder = strtoupper(isset($options['sortorder']) ? (string)$options['sortorder'] : 'ASC');
		$limit = isset($options['limit']) ? (int)$options['limit'] : 20;
		$offset = isset($options['offset']) ? (int)$options['offset'] : 0;

		$allowedOrderColumns = array(
			'user_name' => 'u.user_name',
			'first_name' => 'u.first_name',
			'last_name' => 'u.last_name',
			'email1' => 'u.email1',
			'status' => 'u.status',
			'role_name' => 'r.rolename'
		);
		if (!isset($allowedOrderColumns[$orderBy])) {
			$orderBy = 'last_name';
		}
		if (!in_array($sortOrder, array('ASC', 'DESC'), true)) {
			$sortOrder = 'ASC';
		}
		if ($limit <= 0) $limit = 20;
		if ($offset < 0) $offset = 0;

		if ($isAdmin) {
			// Admin can see all users
			$params = array();
			$query = "SELECT u.id, u.user_name, u.first_name, u.last_name, u.email1, u.status,
						r.rolename AS role_name,
						ubm.parent_user_id,
						ubm.creatorid AS creator_id
					FROM vtiger_users u
					LEFT JOIN vtiger_user2role ur ON ur.userid = u.id
					LEFT JOIN vtiger_role r ON r.roleid = ur.roleid
					LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
					WHERE u.deleted = 0";
		} else {
			// Non-admin users can see their children and created users
			$params = array($currentUserId, $currentUserId);
			$query = "SELECT u.id, u.user_name, u.first_name, u.last_name, u.email1, u.status,
						r.rolename AS role_name,
						ubm.parent_user_id,
						ubm.creatorid AS creator_id
					FROM vtiger_users u
					LEFT JOIN vtiger_user2role ur ON ur.userid = u.id
					LEFT JOIN vtiger_role r ON r.roleid = ur.roleid
					LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
					WHERE u.deleted = 0
					  AND (ubm.parent_user_id = ? OR ubm.creatorid = ?)";
		}

		if ($search !== '') {
			$query .= " AND (u.user_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email1 LIKE ?)";
			$searchLike = '%' . $search . '%';
			array_push($params, $searchLike, $searchLike, $searchLike, $searchLike);
		}
		$query .= " ORDER BY " . $allowedOrderColumns[$orderBy] . " " . $sortOrder . ", u.id ASC";
		$query .= " LIMIT " . (int)$offset . ", " . (int)$limit;

		$result = $db->pquery($query, $params);
		$rows = array();
		$count = $db->num_rows($result);
		for ($i = 0; $i < $count; $i++) {
			$rows[] = $db->query_result_rowdata($result, $i);
		}
		return $rows;
	}

	public static function getManageableUsersCount($currentUserId, $isAdmin = false, $search = '') {
		$db = PearDatabase::getInstance();
		$search = trim((string)$search);
		if ($isAdmin) {
			// Admin can see all users
			$query = "SELECT COUNT(*) AS total
					FROM vtiger_users u
					WHERE u.deleted = 0";
			$params = array();
		} else {
			// Non-admin users can see their children and created users
			$query = "SELECT COUNT(*) AS total
					FROM vtiger_users u
					LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
					WHERE u.deleted = 0
					  AND (ubm.parent_user_id = ? OR ubm.creatorid = ?)";
			$params = array($currentUserId, $currentUserId);
		}

		if ($search !== '') {
			$query .= " AND (u.user_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email1 LIKE ?)";
			$searchLike = '%' . $search . '%';
			array_push($params, $searchLike, $searchLike, $searchLike, $searchLike);
		}

		$result = $db->pquery($query, $params);
		if ($db->num_rows($result) < 1) {
			return 0;
		}
		return (int)$db->query_result($result, 0, 'total');
	}
}

