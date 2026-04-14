<?php
/*+***********************************************************************************
 * Project Edit view — branch scope check (partner check is in generic flow where applicable).
 *************************************************************************************/

class Project_Edit_View extends Vtiger_Edit_View {

	public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		$recordId = (int)$request->get('record');
		if ($recordId) {
			$partnerHelper = 'modules/BranchUsers/helpers/PartnerAccess.php';
			if (file_exists($partnerHelper)) {
				require_once $partnerHelper;
				BranchUsers_PartnerAccess::assertRecordInPartnerScope($recordId);
			}
			$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
			if (file_exists($hierarchyHelper)) {
				require_once $hierarchyHelper;
				BranchUsers_HierarchyAccess::assertProjectInPrivateLineScope($recordId);
			}
		}
		return true;
	}

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
		if (!empty($record) && $request->get('isDuplicate') == true) {
			$recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', '');
			$mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
			foreach ($mandatoryFieldModels as $fieldModel) {
				if ($fieldModel->isReferenceField()) {
					$fieldName = $fieldModel->get('name');
					if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
						$recordModel->set($fieldName, '');
					}
				}
			}
		} else if (!empty($record)) {
			$recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('RECORD_ID', $record);
			$viewer->assign('MODE', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$viewer->assign('MODE', '');
		}
		if (!$this->record) {
			$this->record = $recordModel;
		}

		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAllPurified(), $fieldList);
		foreach ($requestFieldList as $fieldName => $fieldValue) {
			$fieldModel = $fieldList[$fieldName];
			if ($fieldModel->isEditable()) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel(
			$recordModel,
			Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT
		);
		$this->filterAssignedUserFieldByHierarchy($recordStructureInstance, $recordModel);

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$isRelationOperation = $request->get('relationOperation');
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if ($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}

		if ($request->get('returnview')) {
			$request->setViewerReturnValues($viewer);
		}
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
		if ($request->get('displayMode') == 'overlay') {
			$viewer->assign('SCRIPTS', $this->getOverlayHeaderScripts($request));
			$viewer->view('OverlayEditView.tpl', $moduleName);
		} else {
			$viewer->view('EditView.tpl', $moduleName);
		}
	}

	/**
	 * Show only hierarchy-allowed users in Assigned To list for Project edit/create.
	 *
	 * @param Vtiger_RecordStructure_Model $recordStructureInstance
	 * @param Vtiger_Record_Model $recordModel
	 * @return void
	 */
	private function filterAssignedUserFieldByHierarchy($recordStructureInstance, $recordModel) {
		$hierarchyHelper = 'modules/BranchUsers/helpers/HierarchyAccess.php';
		if (!file_exists($hierarchyHelper)) {
			return;
		}
		require_once $hierarchyHelper;
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

		$usersLabelKey = vtranslate('LBL_USERS');
		$groupsLabelKey = vtranslate('LBL_GROUPS');
		$structure = $recordStructureInstance->getStructure();
		foreach ($structure as $blockLabel => $fields) {
			foreach ($fields as $fieldModel) {
				if ((string)$fieldModel->getName() !== 'assigned_user_id') {
					continue;
				}
				$fieldInfo = $fieldModel->getFieldInfo();
				$fieldInfo['picklistvalues'] = array(
					$usersLabelKey => $allowedUsers,
					$groupsLabelKey => array()
				);
				$fieldModel->setFieldInfo($fieldInfo);
				$fieldModel->set('fieldvalue', $currentOwnerId > 0 ? $currentOwnerId : $currentUserId);
			}
		}
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
