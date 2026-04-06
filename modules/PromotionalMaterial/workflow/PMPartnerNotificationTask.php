<?php
/*+**********************************************************************************
 * Legacy workflow task type (optional). Prefer: Invoke custom function → PMNotifyPartners.
 * doTask delegates to pmwf_notifyPartnerUsers() in PromotionalMaterialWorkflowMethods.php
 ************************************************************************************/

require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';

class PMPartnerNotificationTask extends VTTask {

	public $executeImmediately = true;

	public $notify_scope = 'all_partners';
	public $notify_user_ids = '';
	public $notification_title = '';
	public $notification_message = '';

	public function getFieldNames() {
		return array('notify_scope', 'notify_user_ids', 'notification_title', 'notification_message');
	}

	public function doTask($entity) {
		require_once 'modules/PromotionalMaterial/workflow/PromotionalMaterialWorkflowMethods.php';
		pmwf_notifyPartnerUsers($entity);
	}
}
