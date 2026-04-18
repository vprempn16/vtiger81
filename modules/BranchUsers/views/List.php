<?php
/*+**********************************************************************************
 * BranchUsers List view (non-admin user management).
 ************************************************************************************/

class BranchUsers_List_View extends Vtiger_Index_View {
	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$layout = Vtiger_Viewer::getDefaultLayoutName();
		$jsFileNames = array(
			'modules.Vtiger.resources.List',
			'modules.Vtiger.resources.ListSidebar',
			'modules.CustomView.resources.CustomView',
			'modules.Vtiger.resources.Tag',
			"~layouts/$layout/lib/jquery/floatThead/jquery.floatThead.js",
			"~layouts/$layout/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js",
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
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$isAdmin = ($currentUserModel && $currentUserModel->isAdminUser());
		$page = max(1, (int)$request->get('page'));
		$pageLimit = max(1, min(100, (int)$request->get('pageLimit')));
		if ((int)$request->get('pageLimit') <= 0) {
			$pageLimit = 20;
		}
		$search = trim((string)$request->get('search_key'));
		$orderBy = (string)$request->get('orderby');
		$sortOrder = strtoupper((string)$request->get('sortorder'));
		if ($sortOrder !== 'DESC') {
			$sortOrder = 'ASC';
		}
		if ($orderBy === '') {
			$orderBy = 'last_name';
		}
		$offset = ($page - 1) * $pageLimit;
		$options = array(
			'search' => $search,
			'orderby' => $orderBy,
			'sortorder' => $sortOrder,
			'limit' => $pageLimit,
			'offset' => $offset,
		);

		$totalUsers = BranchUsers_UserList_Model::getManageableUsersCount($currentUserId, $isAdmin, $search);
		$totalPages = max(1, (int)ceil($totalUsers / $pageLimit));
		if ($page > $totalPages) {
			$page = $totalPages;
			$offset = ($page - 1) * $pageLimit;
			$options['offset'] = $offset;
		}
		$users = BranchUsers_UserList_Model::getManageableUsers($currentUserId, $isAdmin, $options);

		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE', $request->getModule());
		$viewer->assign('USERS', $users);
		$viewer->assign('CURRENT_USER_ID', $currentUserId);
		$viewer->assign('IS_ADMIN', $isAdmin);
		$viewer->assign('SEARCH_KEY', $search);
		$viewer->assign('ORDERBY', $orderBy);
		$viewer->assign('SORTORDER', $sortOrder);
		$viewer->assign('PAGELIMIT', $pageLimit);
		$viewer->assign('PAGE', $page);
		$viewer->assign('TOTAL_PAGES', $totalPages);
		$viewer->assign('TOTAL_USERS', $totalUsers);
		$viewer->view('ListView.tpl', $request->getModule());
	}
}

