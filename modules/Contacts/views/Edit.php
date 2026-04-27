<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Edit_View extends Vtiger_Edit_View {

	public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		$recordId = (int)$request->get('record');
		if ($recordId > 0) {
			$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
			if (file_exists($hierarchyHelper)) {
				require_once $hierarchyHelper;
				BranchUsers_HierarchyAccess::assertCrmRecordInPrivateLineScope($recordId);
			}
		}
		return true;
	}

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$recordModel = $this->record;
		if(!$recordModel){
			if (!empty($recordId)) {
				$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			} else {
				$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			}
			$this->record = $recordModel;
		}

		$viewer = $this->getViewer($request);
		// Also assign filtered users directly for template use
                $filteredUsers = $this->getFilteredAssignedUsers($recordModel);
                $viewer->assign('FILTERED_ASSIGNED_USERS', $filteredUsers);
		$salutationFieldModel = Vtiger_Field_Model::getInstance('salutationtype', $recordModel->getModule());
		// Fix for http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7851
		$salutationType = $request->get('salutationtype');
		if(!empty($salutationType)){
			$salutationFieldModel->set('fieldvalue', $request->get('salutationtype'));
		} else {
			$salutationFieldModel->set('fieldvalue', $recordModel->get('salutationtype')); 
		}

		$viewer->assign('SALUTATION_FIELD_MODEL', $salutationFieldModel);
		$this->applyHierarchyOwnerPicklist($recordModel);

		parent::process($request);
	}

	/**
	 * Restrict Assigned To to current user hierarchy line.
	 *
	 * @param Vtiger_Record_Model $recordModel
	 * @return void
	 */
	private function applyHierarchyOwnerPicklist($recordModel) {
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
		$currentUserId = (int)$currentUser->id;
		if ($currentUserId <= 0) {
			return;
		}
		$allowedUserIds = BranchUsers_HierarchyAccess::getDescendantUserIds($currentUserId);
		$allowedUserIds[] = $currentUserId;
		$currentOwnerId = (int)$recordModel->get('assigned_user_id');
		if ($currentOwnerId > 0) {
			$allowedUserIds[] = $currentOwnerId;
		}
		$allowedUserIds = array_values(array_unique(array_map('intval', $allowedUserIds)));
		$allowedUsers = $this->getActiveUsersByIds($allowedUserIds);
		if (empty($allowedUsers)) {
			$allowedUsers = array($currentUserId => trim(getUserFullName($currentUserId)));
		}
		$ownerFieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $recordModel->getModule());
		if (!$ownerFieldModel) {
			return;
		}
		$fieldInfo = $ownerFieldModel->getFieldInfo();
		$fieldInfo['picklistvalues'] = array(
			vtranslate('LBL_USERS') => $allowedUsers,
			vtranslate('LBL_GROUPS') => array()
		);
		$ownerFieldModel->setFieldInfo($fieldInfo);
	}

	
        /**
         * Get filtered assigned users based on creatorid logic
         *
         * @param Vtiger_Record_Model $recordModel
         * @return array<int,string>
         */
        private function getFilteredAssignedUsers($recordModel) {
                $currentUserModel = Users_Record_Model::getCurrentUserModel();
                if (!$currentUserModel) {
                        return array();
                }

                // Check if user is admin - if admin, return empty to use default behavior
                if ($currentUserModel->isAdminUser()) {
                        return array();
                }

                $currentUser = vglobal('current_user');
                $currentUserId = (int)$currentUser->id;
                if ($currentUserId <= 0) {
                        return array();
                }

                // Use branch hierarchy logic to get both lower and higher level users in same branch
                require_once 'modules/BranchUsers/models/UserList.php';
                $branchUserIds = BranchUsers_UserList_Model::getBranchHierarchyUsers($currentUserId);
                $allowedUserIds = array_merge(array($currentUserId), $branchUserIds); // Include current user in assigned to options

                $currentOwnerId = (int)$recordModel->get('assigned_user_id');
                if ($currentOwnerId > 0 && !in_array($currentOwnerId, $allowedUserIds)) {
                        $allowedUserIds[] = $currentOwnerId;
                }

                $allowedUserIds = array_values(array_unique(array_map('intval', $allowedUserIds)));
                return $this->getActiveUsersByIds($allowedUserIds);
	}

	/**
	 * @param int[] $ids
	 * @return array<int,string>
	 */
	private function getActiveUsersByIds($ids) {
		$ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids), function ($id) {
			return $id > 0;
		})));
		if (empty($ids)) {
			return array();
		}
		$db = PearDatabase::getInstance();
		$result = $db->pquery(
			"SELECT id, user_name, first_name, last_name FROM vtiger_users WHERE deleted = 0 AND status = 'Active' AND id IN (" . generateQuestionMarks($ids) . ")",
			$ids
		);
		$out = array();
		$n = $db->num_rows($result);
		for ($i = 0; $i < $n; $i++) {
			$id = (int)$db->query_result($result, $i, 'id');
			$firstName = trim((string)$db->query_result($result, $i, 'first_name'));
			$lastName = trim((string)$db->query_result($result, $i, 'last_name'));
			$userName = trim((string)$db->query_result($result, $i, 'user_name'));
			$label = trim($firstName . ' ' . $lastName);
			$out[$id] = ($label !== '') ? $label : $userName;
		}
		return $out;
	}

}
