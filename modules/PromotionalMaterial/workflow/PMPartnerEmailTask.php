<?php
/*+**********************************************************************************
 * Legacy workflow task type (optional). Prefer: Invoke custom function → PMSendPartnerEmail.
 * doTask delegates to pmwf_sendPartnerEmail() in PromotionalMaterialWorkflowMethods.php
 ************************************************************************************/

require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';

class PMPartnerEmailTask extends VTTask {

	public $executeImmediately = true;

	public $emailTemplateId = '';
	public $recepient = '';
	public $fromEmail = '';
	public $emailcc = '';
	public $emailbcc = '';
	public $replyTo = '';

	public function getFieldNames() {
		return array('emailTemplateId', 'recepient', 'fromEmail', 'emailcc', 'emailbcc', 'replyTo');
	}

	public function doTask($entity) {
		require_once 'modules/PromotionalMaterial/workflow/PromotionalMaterialWorkflowMethods.php';
		pmwf_sendPartnerEmail($entity);
	}
}
