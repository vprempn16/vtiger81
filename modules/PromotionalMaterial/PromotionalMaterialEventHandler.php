<?php
/*+**********************************************************************************
 * PromotionalMaterialEventHandler — vtiger.entity.aftersave
 * Notifies Partner-role users when a record becomes Published (once per publish).
 ************************************************************************************/

require_once 'include/events/VTEventHandler.inc';
require_once 'include/events/VTEntityDelta.php';
require_once 'modules/VDNotifierPro/models/Record.php';
require_once 'modules/Emails/mail.php';

class PromotionalMaterialEventHandler extends VTEventHandler {

	public function handleEvent($eventName, $data) {
		if ($eventName !== 'vtiger.entity.aftersave') {
			return;
		}
		if ($data->getModuleName() !== 'PromotionalMaterial') {
			return;
		}

		// Default: use Settings → Workflows tasks PMPartnerNotificationTask / PMPartnerEmailTask instead.
		if (!defined('PM_PROMOTIONAL_USE_LEGACY_EVENT_NOTIFIER') || !PM_PROMOTIONAL_USE_LEGACY_EVENT_NOTIFIER) {
			return;
		}

		global $adb, $current_user, $log, $site_URL;

		try {
			$recordId = $data->getId();
			if (empty($recordId)) {
				return;
			}

			$currentStatus = $data->get('promotional_status');
			if ($currentStatus !== 'Published') {
				return;
			}

			$vtEntityDelta = new VTEntityDelta();
			$delta = $vtEntityDelta->getEntityDelta('PromotionalMaterial', $recordId, true);
			$oldStatus = '';
			if (isset($delta['promotional_status']['oldValue'])) {
				$oldStatus = $delta['promotional_status']['oldValue'];
			}
			if ($oldStatus === 'Published') {
				return;
			}

			$result = $adb->pquery(
				'SELECT pm.title, pm.promotional_status, ce.modifiedby FROM vtiger_promotionalmaterial pm
				 INNER JOIN vtiger_crmentity ce ON pm.promotionalmaterialid = ce.crmid
				 WHERE pm.promotionalmaterialid = ? AND ce.deleted = 0',
				array($recordId)
			);
			if ($adb->num_rows($result) == 0) {
				return;
			}
			$row = $adb->fetch_array($result);
			$title = !empty($row['title']) ? $row['title'] : 'Promotional Material';
			$modiUserId = !empty($row['modifiedby']) ? $row['modifiedby'] : $current_user->id;

			self::notifyPartners($recordId, $title, $modiUserId, $site_URL);
		} catch (Exception $e) {
			if (isset($log)) {
				$log->error('PromotionalMaterialEventHandler: ' . $e->getMessage());
			}
		}
	}

	private static function notifyPartners($recordId, $title, $modiUserId, $site_URL) {
		global $adb, $current_user, $log;

		if (!vtlib_isModuleActive('VDNotifierPro')) {
			return;
		}

		$partnerUserIds = self::getPartnerUserIds();
		if (empty($partnerUserIds)) {
			return;
		}

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'PromotionalMaterial');
		$link = str_replace('index.php?', '', $recordModel->getDetailViewUrl());

		foreach ($partnerUserIds as $userId) {
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
			$VDNotifier->title = vtranslate('LBL_PROMOTIONAL_MATERIAL_PUBLISHED', 'PromotionalMaterial')
				. ': ' . $title;
			$VDNotifier->save();

			self::sendEmailNotification($userId, $recordId, $title, $site_URL);
		}
	}

	private static function getPartnerUserIds() {
		global $adb;

		if (!class_exists('PromotionalMaterial')) {
			require_once 'modules/PromotionalMaterial/PromotionalMaterial.php';
		}
		$partnerRoleId = PromotionalMaterial::getPartnerRoleId();
		$result = $adb->pquery(
			'SELECT vtiger_users.id FROM vtiger_users
			 INNER JOIN vtiger_user2role ON vtiger_user2role.userid = vtiger_users.id
			 WHERE vtiger_users.deleted = 0 AND vtiger_user2role.roleid = ?',
			array($partnerRoleId)
		);

		$ids = array();
		$rows = $adb->num_rows($result);
		for ($i = 0; $i < $rows; $i++) {
			$ids[] = $adb->query_result($result, $i, 'id');
		}
		return $ids;
	}

	private static function sendEmailNotification($userId, $recordId, $title, $site_URL) {
		global $adb, $current_user, $log;

		$userResult = $adb->pquery(
			'SELECT email1, first_name, last_name FROM vtiger_users WHERE id = ? AND deleted = 0',
			array($userId)
		);
		if ($adb->num_rows($userResult) == 0) {
			return;
		}
		$userRow = $adb->fetch_array($userResult);
		$userEmail = $userRow['email1'];
		if (empty($userEmail)) {
			return;
		}

		$base = rtrim($site_URL, '/');
		$recordUrl = $base . '/index.php?module=PromotionalMaterial&view=Detail&record=' . $recordId;

		$subject = vtranslate('LBL_PROMOTIONAL_MATERIAL_PUBLISHED', 'PromotionalMaterial') . ': ' . $title;
		$body = "Dear Partner,\n\n";
		$body .= "A new promotional material has been published.\n\n";
		$body .= 'Title: ' . $title . "\n";
		$body .= 'Link: ' . $recordUrl . "\n\n";
		$body .= "You can open the record to view details and download attachments.\n";

		$from_name = trim($current_user->first_name . ' ' . $current_user->last_name);
		if ($from_name === '') {
			$from_name = $current_user->user_name;
		}
		$from_email = '';
		if (!empty($current_user->email1)) {
			$from_email = $current_user->email1;
		}

		send_mail('PromotionalMaterial', $userEmail, $from_name, $from_email, $subject, $body);
	}
}
