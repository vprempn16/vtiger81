<?php
/*+**********************************************************************************
 * PromotionalMaterial — Workflow "Invoke custom function" handlers.
 *
 * Register in com_vtiger_workflowtasks_entitymethod (see PromotionalMaterial::registerWorkflowEntityMethods).
 * In Settings → Workflows → Add task → Invoke custom function, choose:
 *   - PMNotifyPartners        (VDNotifier Pro, default copy)
 *   - PMSendPartnerEmail      (email using template "PMPartnerEmailTemplate", module Users)
 ************************************************************************************/

if (!function_exists('pmwf_notifyPartnerUsers')) {

	function pmwf_notifyPartnerUsers($entityData) {
		global $adb;
		
		// Debug logging
		$logFile = 'storage/pm_workflow_debug.log';
		file_put_contents($logFile, date('Y-m-d H:i:s') . " - pmwf_notifyPartnerUsers called\n", FILE_APPEND);

		if (!is_object($entityData) || $entityData->getModuleName() !== 'PromotionalMaterial') {
			file_put_contents($logFile, date('Y-m-d H:i:s') . " - Invalid entity data or wrong module\n", FILE_APPEND);
			return;
		}
		if (!vtlib_isModuleActive('VDNotifierPro') || !file_exists('modules/VDNotifierPro/models/Record.php')) {
			file_put_contents($logFile, date('Y-m-d H:i:s') . " - VDNotifierPro not active or missing\n", FILE_APPEND);
			return;
		}

		require_once 'modules/VDNotifierPro/models/Record.php';
		if (!class_exists('PromotionalMaterial')) {
			require_once 'modules/PromotionalMaterial/PromotionalMaterial.php';
		}

		$wsId = $entityData->getId();
		$parts = vtws_getIdComponents($wsId);
		$recordId = $parts[1];

		$title = $entityData->get('title');
		if ($title === null || $title === '') {
			$title = vtranslate('PromotionalMaterial', 'PromotionalMaterial');
		}

		$defaultTitle = vtranslate('LBL_PM_WF_DEFAULT_NOTIFY_TITLE', 'PromotionalMaterial');
		$notifierTitle = $defaultTitle . ': ' . $title;

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'PromotionalMaterial');
		$link = str_replace('index.php?', '', $recordModel->getDetailViewUrl());

		$modiUserId = pmwf_getModifiedByUserId($entityData);
		$admin = Users::getActiveAdminUser();
		if (empty($modiUserId)) {
			$modiUserId = $admin->id;
		}

		$partnerIds = pmwf_getPartnerUserIds($adb);
		foreach ($partnerIds as $userId) {
			if ((int) $userId === (int) $modiUserId) {
				continue;
			}
			$VDNotifier = new VDNotifierPro_Record_Model();
			$VDNotifier->userid = $userId;
			$VDNotifier->modulename = 'PromotionalMaterial';
			$VDNotifier->crmid = $recordId;
			$VDNotifier->modiuserid = $modiUserId;
			$VDNotifier->action = 'UPDATED';
			$VDNotifier->modifiedtime = date('Y-m-d H:i:s');
			$VDNotifier->link = $link;
			$VDNotifier->title = $notifierTitle;
			if (strlen($VDNotifier->title) > 500) {
				$VDNotifier->title = substr($VDNotifier->title, 0, 497) . '...';
			}
			$VDNotifier->save();
		}
	}

	function pmwf_sendPartnerEmail($entityData) {
		global $adb, $site_URL;

		// Start logging
		$logFile = 'storage/pm_workflow_email.log';
		$log = function($message) use ($logFile) {
			$timestamp = date('Y-m-d H:i:s');
			$logMessage = "[$timestamp] $message\n";
			file_put_contents($logFile, $logMessage, FILE_APPEND);
		};

		$log("=== Starting pmwf_sendPartnerEmail ===");

		if (!is_object($entityData) || $entityData->getModuleName() !== 'PromotionalMaterial') {
			$log("ERROR: Invalid entity data or wrong module");
			return;
		}
		$log("Entity data validated successfully");

		require_once 'include/utils/CommonUtils.php';
		require_once 'vtlib/Vtiger/Functions.php';
		require_once 'modules/Emails/mail.php';
		if (!class_exists('PromotionalMaterial')) {
			require_once 'modules/PromotionalMaterial/PromotionalMaterial.php';
		}

		$wsId = $entityData->getId();
		$recordId = vtws_getIdComponents($wsId)[1];
		$log("Record ID: $recordId, WS ID: $wsId");

		$res = $adb->pquery(
			'SELECT templateid, subject, body FROM vtiger_emailtemplates
			 WHERE templatename = ? AND module = ? AND deleted = ? ORDER BY templateid DESC LIMIT 1',
			array('PMPartnerEmailTemplate', 'Users', 0)
		);
		if ($adb->num_rows($res) === 0) {
			$log("ERROR: Email template 'PMPartnerEmailTemplate' not found in module 'Users'");
			return;
		}
		$row = $adb->fetch_array($res);
		$templateSubject = $row['subject'];
		$templateBody = $row['body'];
		$log("Email template found. Template ID: {$row['templateid']}");

		$detailUrl = rtrim($site_URL, '/') . '/index.php?module=PromotionalMaterial&view=Detail&record=' . $recordId;
		$pmTitle = $entityData->get('title');
		if ($pmTitle === null || $pmTitle === '') {
			$pmTitle = '';
		}
		$log("Detail URL: $detailUrl, Title: '$pmTitle'");

		$admin = Users::getActiveAdminUser();
		$from_name = trim($admin->first_name . ' ' . $admin->last_name);
		if ($from_name === '') {
			$from_name = $admin->user_name;
		}
		$from_email = !empty($admin->email1) ? $admin->email1 : '';
		$log("From: $from_name <$from_email>");

		$partnerIds = pmwf_getPartnerUserIds($adb);
		$log("Found " . count($partnerIds) . " partner users: " . implode(', ', $partnerIds));

		if (empty($partnerIds)) {
			$log("WARNING: No partner users found");
		}

		$emailsSent = 0;
		$emailsFailed = 0;

		foreach ($partnerIds as $userId) {
			$log("Processing user ID: $userId");

			$ur = $adb->pquery(
				'SELECT email1, first_name, last_name FROM vtiger_users WHERE id = ? AND deleted = 0',
				array($userId)
			);
			if ($adb->num_rows($ur) === 0) {
				$log("WARNING: User $userId not found or deleted");
				continue;
			}
			$urow = $adb->fetch_array($ur);
			$toEmail = trim($urow['email1']);
			if ($toEmail === '') {
				$log("WARNING: User $userId has no email address");
				continue;
			}
			$log("User $userId email: $toEmail");

			$subject = decode_html(Vtiger_Functions::getMergedDescription($templateSubject, $userId, 'Users'));
			$body = Vtiger_Functions::getMergedDescription($templateBody, $userId, 'Users');

			$subject = str_replace(
				array('###PM_DETAIL_URL###', '###PM_RECORD_TITLE###'),
				array($detailUrl, $pmTitle),
				$subject
			);
			$body = str_replace(
				array('###PM_DETAIL_URL###', '###PM_RECORD_TITLE###'),
				array($detailUrl, $pmTitle),
				$body
			);

			$log("Merged subject: '$subject'");

			$logo = (stripos($body, '<img src="cid:logo" />') !== false) ? 1 : '';

			// Check mail configuration before sending
			$log("Checking mail configuration...");
			$mailServerConfig = $adb->pquery("SELECT * FROM vtiger_systems WHERE server_type = ?", array('email'));
			if ($adb->num_rows($mailServerConfig) === 0) {
				$log("ERROR: No outgoing mail server configured in vtiger_systems");
			} else {
				$mailConfig = $adb->fetch_array($mailServerConfig);
				$log("Mail server found: {$mailConfig['server']}, Port: {$mailConfig['server_port']}, From: {$mailConfig['from_email']}");
			}

			$log("Attempting to send email to $toEmail...");
			$sendResult = send_mail('PromotionalMaterial', $toEmail, $from_name, $from_email, $subject, $body, '', '', '', '', $logo);

			if ($sendResult) {
				$log("SUCCESS: Email sent to $toEmail");
				$emailsSent++;
			} else {
				$log("ERROR: Failed to send email to $toEmail");
				$emailsFailed++;
			}
		}

		$log("=== Email sending completed ===");
		$log("Results: $emailsSent sent, $emailsFailed failed");
		$log("================================");
	}

	function pmwf_getPartnerUserIds($adb) {
		if (!class_exists('PromotionalMaterial')) {
			require_once 'modules/PromotionalMaterial/PromotionalMaterial.php';
		}
		$roleId = PromotionalMaterial::getPartnerRoleId();
		$result = $adb->pquery(
			'SELECT vtiger_users.id FROM vtiger_users
			 INNER JOIN vtiger_user2role ON vtiger_user2role.userid = vtiger_users.id
			 WHERE vtiger_users.deleted = 0 AND vtiger_user2role.roleid = ?',
			array($roleId)
		);
		$ids = array();
		for ($i = 0; $i < $adb->num_rows($result); $i++) {
			$ids[] = $adb->query_result($result, $i, 'id');
		}
		return $ids;
	}

	function pmwf_getModifiedByUserId($entityData) {
		$mb = $entityData->get('modifiedby');
		if (empty($mb)) {
			$au = $entityData->get('assigned_user_id');
			if (!empty($au) && strpos($au, 'x') !== false) {
				return vtws_getIdComponents($au)[1];
			}
			return null;
		}
		if (strpos($mb, 'x') !== false) {
			return vtws_getIdComponents($mb)[1];
		}
		return $mb;
	}
}
