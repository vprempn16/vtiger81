<?php
/*+**********************************************************************************
 * BranchUsers Edit view (create/edit vtiger users through controlled actions).
 ************************************************************************************/

class BranchUsers_Edit_View extends Vtiger_Index_View {
	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$jsFileNames = array(
			'modules.Vtiger.resources.Edit',
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return array_merge($headerScriptInstances, $jsScriptInstances);
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$layout = Vtiger_Viewer::getDefaultLayoutName();
		$cssFileNames = array(
			"~layouts/$layout/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return array_merge($headerCssInstances, $cssInstances);
	}

	public function requiresPermission(Vtiger_Request $request) {
		// Enforce permissions in checkPermission() to allow explicit admin bypass.
		return array();
	}

	public function checkPermission(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel && $currentUserModel->isAdminUser()) {
			return true;
		}
		$moduleModel = Vtiger_Module_Model::getInstance($request->getModule());
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if (!$moduleModel || !$userPrivilegesModel->hasModulePermission($moduleModel->getId())) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		return true;
	}

	public function process(Vtiger_Request $request) {
		$currentUser = vglobal('current_user');
		$currentUserId = (int)$currentUser->id;
		$currentUserLabel = trim(getUserFullName($currentUserId));
		if ($currentUserLabel === '') {
			$currentUserLabel = getUserName($currentUserId);
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$isAdmin = ($currentUserModel && $currentUserModel->isAdminUser());
		$recordId = $request->get('record');
		$currentUserRoleId = (string)$currentUserModel->get('roleid');

		require_once 'modules/BranchUsers/helpers/PartnerAccess.php';
		$userRow = null;
		$selectedRoleId = '';
		$selectedParentUserId = null;
		$selectedParentUserLabel = '';
		if (!empty($recordId)) {
			if (!BranchUsers_PartnerAccess::canManageUser((int)$recordId)) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
			$db = PearDatabase::getInstance();
			$result = $db->pquery(
				"SELECT u.id, u.user_name, u.first_name, u.last_name, u.email1, u.status, ur.roleid
				 FROM vtiger_users u
				 LEFT JOIN vtiger_user2role ur ON ur.userid = u.id
				 WHERE u.id = ? AND u.deleted = 0",
				array((int)$recordId)
			);
			if ($db->num_rows($result) < 1) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}
			$userRow = $db->fetchByAssoc($result, 0);
			if (!empty($userRow['roleid'])) {
				$selectedRoleId = (string)$userRow['roleid'];
			}

			$mapResult = $db->pquery(
				"SELECT parent_user_id FROM vtiger_user_branch_map WHERE user_id = ?",
				array((int)$recordId)
			);
			if ($db->num_rows($mapResult) > 0) {
				$selectedParentUserId = (int)$db->query_result($mapResult, 0, 'parent_user_id');
				if ($selectedParentUserId > 0) {
					$selectedParentUserLabel = trim(getUserFullName($selectedParentUserId));
					if ($selectedParentUserLabel === '') {
						$selectedParentUserLabel = getUserName($selectedParentUserId);
					}
				}
			}
		}
		$availableRoles = $this->getAvailableRoles($currentUserRoleId);
		$firstChildRoleId = $this->getFirstChildRoleId($currentUserRoleId);

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE', $request->getModule());
		$viewer->assign('RECORD_ID', $recordId ? (int)$recordId : null);
		$viewer->assign('USER', $userRow);
		$viewer->assign('CURRENT_USER_ID', $currentUserId);
		$viewer->assign('CURRENT_USER_ROLE_ID', $currentUserRoleId);
		$viewer->assign('CURRENT_USER_LABEL', $currentUserLabel);
		$viewer->assign('AVAILABLE_ROLES', $availableRoles);
		$viewer->assign('SELECTED_ROLE_ID', $selectedRoleId);
		$viewer->assign('FIRST_CHILD_ROLE_ID', $firstChildRoleId);
		$viewer->assign('SELECTED_PARENT_USER_ID', $selectedParentUserId);
		$viewer->assign('SELECTED_PARENT_USER_LABEL', $selectedParentUserLabel);
		$viewer->view('EditView.tpl', $request->getModule());
	}

	private function getAvailableRoles($currentRoleId) {
		$db = PearDatabase::getInstance();
		$roleResult = $db->pquery(
			"SELECT parentrole FROM vtiger_role WHERE roleid = ?",
			array($currentRoleId)
		);
		$parentRole = ($db->num_rows($roleResult) > 0) ? $db->query_result($roleResult, 0, 'parentrole') : '';
		$result = $db->pquery(
			"SELECT roleid, rolename, depth FROM vtiger_role
			 WHERE roleid = ? OR parentrole LIKE ?
			 ORDER BY parentrole ASC",
			array($currentRoleId, $parentRole . '::%')
		);

		$roles = array();
		$rows = $db->num_rows($result);
		for ($i = 0; $i < $rows; $i++) {
			$roles[] = $db->query_result_rowdata($result, $i);
		}
		return $roles;
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
}

