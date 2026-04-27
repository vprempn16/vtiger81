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
			// Non-admin users can see all users in their branch hierarchy (all descendants)
			$branchUserIds = self::getBranchDescendants($currentUserId);
			if (empty($branchUserIds)) {
				// No descendants found, return empty result
				$params = array();
				$query = "SELECT u.id, u.user_name, u.first_name, u.last_name, u.email1, u.status,
							r.rolename AS role_name,
							ubm.parent_user_id,
							ubm.creatorid AS creator_id
						FROM vtiger_users u
						LEFT JOIN vtiger_user2role ur ON ur.userid = u.id
						LEFT JOIN vtiger_role r ON r.roleid = ur.roleid
						LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
						WHERE u.deleted = 0 AND 1=0"; // Always false condition
			} else {
				$placeholders = str_repeat('?,', count($branchUserIds) - 1) . '?';
				$params = array_merge($branchUserIds);
				$query = "SELECT u.id, u.user_name, u.first_name, u.last_name, u.email1, u.status,
							r.rolename AS role_name,
							ubm.parent_user_id,
							ubm.creatorid AS creator_id
						FROM vtiger_users u
						LEFT JOIN vtiger_user2role ur ON ur.userid = u.id
						LEFT JOIN vtiger_role r ON r.roleid = ur.roleid
						LEFT JOIN vtiger_user_branch_map ubm ON ubm.user_id = u.id
						WHERE u.deleted = 0
						  AND u.id IN ($placeholders)";
			}
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
			// Non-admin users can see all users in their branch hierarchy (all descendants)
			$branchUserIds = self::getBranchDescendants($currentUserId);
			if (empty($branchUserIds)) {
				// No descendants found, return 0
				return 0;
			} else {
				$placeholders = str_repeat('?,', count($branchUserIds) - 1) . '?';
				$params = array_merge($branchUserIds);
				$query = "SELECT COUNT(*) AS total
						FROM vtiger_users u
						WHERE u.deleted = 0
						  AND u.id IN ($placeholders)";
			}
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

	/**
	 * Get all users in branch hierarchy (all descendants of current user)
	 *
	 * @param int $parentUserId
	 * @return array<int> List of user IDs in the branch
	 */
	public static function getBranchDescendants($parentUserId) {
		$db = PearDatabase::getInstance();
		$allDescendants = array(); // Don't include current user in list view
		$toProcess = array($parentUserId);
		
		while (!empty($toProcess)) {
			$currentParent = array_shift($toProcess);
			
			// Find all direct children of current parent
			$result = $db->pquery(
				"SELECT user_id FROM vtiger_user_branch_map WHERE parent_user_id = ?",
				array($currentParent)
			);
			
			$children = array();
			$count = $db->num_rows($result);
			for ($i = 0; $i < $count; $i++) {
				$childId = (int)$db->query_result($result, $i, 'user_id');
				if ($childId > 0 && !in_array($childId, $allDescendants)) {
					$children[] = $childId;
					$allDescendants[] = $childId;
				}
			}
			
			// Add children to processing queue to find their descendants
			$toProcess = array_merge($toProcess, $children);
		}
		
		return $allDescendants;
	}
	
	/**
	 * Get all users in the same branch hierarchy (both descendants and ancestors)
	 * @param int $currentUserId
	 * @return array Array of user IDs in the same branch hierarchy (excluding current user)
	 */
	public static function getBranchHierarchyUsers($currentUserId) {
		$db = PearDatabase::getInstance();
		$hierarchyUsers = array();
		
		// Get all descendants (lower-level users)
		$descendants = self::getBranchDescendants($currentUserId);
		$hierarchyUsers = array_merge($hierarchyUsers, $descendants);
		
		// Get all ancestors (higher-level users)
		$ancestors = self::getBranchAncestors($currentUserId);
		$hierarchyUsers = array_merge($hierarchyUsers, $ancestors);
		
		// Remove duplicates and exclude current user
		$hierarchyUsers = array_unique($hierarchyUsers);
		$hierarchyUsers = array_filter($hierarchyUsers, function($userId) use ($currentUserId) {
			return $userId != $currentUserId;
		});
		
		return array_values($hierarchyUsers);
	}
	
	/**
	 * Get all ancestors (higher-level users) in the branch hierarchy
	 * @param int $userId
	 * @return array Array of ancestor user IDs
	 */
	public static function getBranchAncestors($userId) {
		$db = PearDatabase::getInstance();
		$ancestors = array();
		$currentUserId = $userId;
		
		// Trace up the hierarchy to find all ancestors
		while ($currentUserId > 0) {
			$result = $db->pquery(
				"SELECT parent_user_id FROM vtiger_user_branch_map WHERE user_id = ?",
				array($currentUserId)
			);
			
			if ($db->num_rows($result) > 0) {
				$parentId = (int)$db->query_result($result, 0, 'parent_user_id');
				if ($parentId > 0 && !in_array($parentId, $ancestors)) {
					$ancestors[] = $parentId;
					$currentUserId = $parentId;
				} else {
					break; // No more parents or cycle detected
				}
			} else {
				break; // No parent found
			}
		}
		
		return $ancestors;
	}
}

