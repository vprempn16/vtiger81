<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
        // Basic Strings
        'PromotionalMaterial' => 'PromotionalMaterial',
	'LBL_PROMOTIONAL_MATERIAL_INFORMATION' => 'Promotional Material Information',
	'LBL_PROMOTIONAL_MATERIAL_PUBLISHED' => 'New promotional material published',
	'LBL_PM_NOTIFY_DEFAULT_TITLE' => 'Promotional material update',
	'LBL_PM_WF_DEFAULT_NOTIFY_TITLE' => 'Promotional material update',

	'LBL_PM_WF_NOTIFY_HELP' => 'Sends an in-app notification (VDNotifier Pro) when the workflow runs. Choose all Partner-role users or a fixed list of user IDs.',
	'LBL_PM_WF_NOTIFY_AUDIENCE' => 'Notify',
	'LBL_PM_WF_NOTIFY_ALL_PARTNERS' => 'All users with Partner role',
	'LBL_PM_WF_NOTIFY_SPECIFIC_USERS' => 'Specific user IDs only',
	'LBL_PM_WF_NOTIFY_USER_IDS' => 'User IDs',
	'LBL_PM_WF_NOTIFY_USER_IDS_PLACEHOLDER' => 'e.g. 12, 34, 56',
	'LBL_PM_WF_NOTIFY_USER_IDS_HELP' => 'Comma- or space-separated vtiger user IDs (Admin → Users). Used only when “Specific user IDs” is selected.',
	'LBL_PM_WF_NOTIFY_TITLE' => 'Notification title',
	'LBL_PM_WF_NOTIFY_TITLE_PH' => 'e.g. New material: $title',
	'LBL_PM_WF_NOTIFY_MESSAGE' => 'Extra detail (optional)',
	'LBL_PM_WF_NOTIFY_MESSAGE_HELP' => 'Optional. Merge fields supported. If it differs from the title, it is added after the title in the notification (truncated if long).',

	'LBL_PM_WF_EMAIL_HELP' => 'Sends an email using a template from Settings → Email Templates for this module. Recipients can be typed addresses or merge fields (same as standard Email task).',
	'LBL_PM_WF_EMAIL_NO_TEMPLATES' => 'No email templates were found for this module. Create templates with module = PromotionalMaterial (or your workflow module) under Email Templates.',
);
