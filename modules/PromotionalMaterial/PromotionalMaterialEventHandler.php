<?php
/*+**********************************************************************************
 * PromotionalMaterialEventHandler.php
 * Handles event triggers for Promotional Material module
 * Sends notifications to Partners when materials are published
 ************************************************************************************/

require_once('modules/VDNotifierPro/VDNotifierPro.php');

class PromotionalMaterialEventHandler {

	/**
	 * Handle after save events for Promotional Material
	 * Sends notifications to Partners when status changes to Published
	 */
	public static function handleAfterSave(&$entityData) {
		global $adb, $current_user, $log;

		try {
			if (empty($entityData)) {
				return;
			}

			// Get the record ID
			$recordId = isset($entityData->id) ? $entityData->id : null;
			if (empty($recordId)) {
				return;
			}

			// Check if this is a Promotional Material record
			$moduleId = getTabid('PromotionalMaterial');
			if (empty($moduleId)) {
				return;
			}

			// Get the current record details
			$result = $adb->pquery(
				'SELECT pm.*, ce.createdtime, ce.smownerid FROM vtiger_promotionalmaterial pm 
				 LEFT JOIN vtiger_crmentity ce ON pm.promotionalmaterialid = ce.crmid 
				 WHERE pm.promotionalmaterialid = ?',
				array($recordId)
			);

			if ($adb->num_rows($result) == 0) {
				return;
			}

			$recordData = $adb->fetch_array($result);
			$status = isset($recordData['status']) ? $recordData['status'] : 'Draft';

			// Only send notifications if published
			if ($status !== 'Published') {
				return;
			}

			// Send notifications to all Partners
			self::notifyPartners($recordId, $recordData);

		} catch (Exception $e) {
			$log->error('PromotionalMaterialEventHandler Error: ' . $e->getMessage());
		}
	}

	/**
	 * Send notifications to all Partner role users
	 */
	private static function notifyPartners($recordId, $recordData) {
		global $adb, $log;

		try {
			$title = isset($recordData['title']) ? $recordData['title'] : 'Promotional Material';

			// Get all users with Partner role (H6)
			$partnerUsers = self::getPartnerUsers();

			if (empty($partnerUsers)) {
				return;
			}

			// Send notification to each partner
			foreach ($partnerUsers as $userId) {
				// Send VDNotifierPro notification
				self::sendNotification($userId, $recordId, $title);

				// Send Email notification
				self::sendEmailNotification($userId, $recordId, $title);
			}

		} catch (Exception $e) {
			$log->error('notifyPartners Error: ' . $e->getMessage());
		}
	}

	/**
	 * Get all users with Partner role
	 */
	private static function getPartnerUsers() {
		global $adb;

		$partnerUsers = array();

		try {
			// Query users with Partner role (H6)
			$result = $adb->pquery(
				'SELECT id FROM vtiger_users WHERE deleted = 0 AND user_name IN 
				 (SELECT user_name FROM vtiger_user2role WHERE roleid = ?)',
				array('H6')
			);

			while ($row = $adb->fetch_array($result)) {
				$partnerUsers[] = $row['id'];
			}

		} catch (Exception $e) {
			// Log error
		}

		return $partnerUsers;
	}

	/**
	 * Send VDNotifierPro notification
	 */
	private static function sendNotification($userId, $recordId, $title) {
		global $adb, $log;

		try {
			// Check if VDNotifierPro is available
			if (!class_exists('VDNotifierPro')) {
				return;
			}

			$notifier = new VDNotifierPro();
			$moduleId = getTabid('PromotionalMaterial');

			$notification = array(
				'user_id' => $userId,
				'record_id' => $recordId,
				'module_id' => $moduleId,
				'title' => 'New Promotional Material Published',
				'description' => 'A new promotional material "' . $title . '" has been published for you.',
				'type' => 'promotional_material_published',
				'action_url' => 'index.php?module=PromotionalMaterial&action=DetailView&record=' . $recordId,
				'created_time' => date('Y-m-d H:i:s')
			);

			// Insert notification to database or VDNotifierPro stream
			$adb->pquery(
				'INSERT INTO vtiger_vdnotifierpro (user_id, record_id, module_id, title, description, type, action_url, created_time, is_read) 
				 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)',
				array(
					$notification['user_id'],
					$notification['record_id'],
					$notification['module_id'],
					$notification['title'],
					$notification['description'],
					$notification['type'],
					$notification['action_url'],
					$notification['created_time']
				)
			);

		} catch (Exception $e) {
			$log->error('sendNotification Error: ' . $e->getMessage());
		}
	}

	/**
	 * Send Email notification to Partner
	 */
	private static function sendEmailNotification($userId, $recordId, $title) {
		global $adb, $log;

		try {
			// Get user email
			$userResult = $adb->pquery(
				'SELECT email1 FROM vtiger_users WHERE id = ?',
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

			// Get instance URL
			$instanceUrl = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
			$recordUrl = 'https://' . $instanceUrl . '/index.php?module=PromotionalMaterial&action=DetailView&record=' . $recordId;

			// Compose email
			$subject = 'New Promotional Material: ' . $title;
			$body = "Dear Partner,\n\n";
			$body .= "A new promotional material has been published for you.\n\n";
			$body .= "Title: " . $title . "\n";
			$body .= "View Details: " . $recordUrl . "\n\n";
			$body .= "You can now download the attached documents.\n\n";
			$body .= "Best regards,\n";
			$body .= "Your CRM System\n";

			// Use Vtiger's mail utility
			if (function_exists('send_mail')) {
				send_mail('Notifications', $userEmail, $subject, $body);
			}

		} catch (Exception $e) {
			$log->error('sendEmailNotification Error: ' . $e->getMessage());
		}
	}
}

/**
 * Event handler registration
 * This function is called by Vtiger's event system
 */
if (!function_exists('promotionalmaterial_event_handler')) {
	function promotionalmaterial_event_handler(&$entityData) {
		PromotionalMaterialEventHandler::handleAfterSave($entityData);
	}
}
?>
