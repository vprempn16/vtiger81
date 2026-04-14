<?php
/*+**********************************************************************************
 * BranchUsers SavePassword action.
 *
 * IMPORTANT:
 * - Validates canManageUser under real user context.
 * - Uses admin context only for Users::change_password().
 ************************************************************************************/

class BranchUsers_SavePassword_Action extends Vtiger_Action_Controller {
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
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		require_once 'modules/BranchUsers/helpers/PartnerAccess.php';

		$recordId = (int)$request->get('record');
		if ($recordId <= 0 || !BranchUsers_PartnerAccess::canManageUser($recordId)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}

		$newPassword = (string)$request->get('new_password');
		if (trim($newPassword) === '') {
			throw new AppException(vtranslate('LBL_NEW_PASSWORD', 'Users') . ' ' . vtranslate('LBL_IS_REQUIRED', 'Vtiger'));
		}
		if (!$this->isPasswordStrong($newPassword)) {
			throw new AppException(vtranslate('LBL_PASSWORDNOTSTRONG', 'Vtiger'));
		}

		global $current_user;
		$backupUser = $current_user;
		$current_user = Users::getActiveAdminUser();
		try {
			$user = new Users();
			$user->retrieve_entity_info($recordId, 'Users');
			$user->id = $recordId;
			$user->change_password('', $newPassword, false);
		} finally {
			$current_user = $backupUser;
		}

		$this->emitSuccessResponse($request, array('id' => $recordId, 'message' => 'Changed password successfully'));
	}

	private function isPasswordStrong($password) {
		$runtimeConfigs = Vtiger_Runtime_Configs::getInstance();
		$regex = $runtimeConfigs->getValidationRegex('password_regex');
		if (empty($regex)) {
			return true;
		}
		return (preg_match('/' . $regex . '/i', $password) === 1);
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

