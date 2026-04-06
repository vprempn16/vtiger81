{*+**********************************************************************************
 * Workflow task: VDNotifierPro notification for Promotional Material (and same module workflows).
 *************************************************************************************}
{strip}
<div class="row" id="PMPartnerNotificationTaskContainer">
	<div class="col-sm-12 col-xs-12">
		<p class="help-block">
			{vtranslate('LBL_PM_WF_NOTIFY_HELP', 'PromotionalMaterial')}
		</p>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_PM_WF_NOTIFY_AUDIENCE', 'PromotionalMaterial')}<span class="redColor">*</span>
			</div>
			<div class="col-sm-9 col-xs-9">
				<select name="notify_scope" class="select2" style="min-width:280px;">
					<option value="all_partners" {if $TASK_OBJECT->notify_scope eq 'all_partners' || empty($TASK_OBJECT->notify_scope)}selected{/if}>
						{vtranslate('LBL_PM_WF_NOTIFY_ALL_PARTNERS', 'PromotionalMaterial')}
					</option>
					<option value="user_ids" {if $TASK_OBJECT->notify_scope eq 'user_ids'}selected{/if}>
						{vtranslate('LBL_PM_WF_NOTIFY_SPECIFIC_USERS', 'PromotionalMaterial')}
					</option>
				</select>
			</div>
		</div>

		<div class="row form-group" id="pm_wf_notify_user_ids_row">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_PM_WF_NOTIFY_USER_IDS', 'PromotionalMaterial')}
			</div>
			<div class="col-sm-9 col-xs-9">
				<input type="text" name="notify_user_ids" class="inputElement" style="width:100%;"
					placeholder="{vtranslate('LBL_PM_WF_NOTIFY_USER_IDS_PLACEHOLDER', 'PromotionalMaterial')}"
					value="{$TASK_OBJECT->notify_user_ids}" />
				<span class="help-block">{vtranslate('LBL_PM_WF_NOTIFY_USER_IDS_HELP', 'PromotionalMaterial')}</span>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_PM_WF_NOTIFY_TITLE', 'PromotionalMaterial')}<span class="redColor">*</span>
			</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="notification_title" class="inputElement" data-rule-required="true" style="width:100%;"
					value="{$TASK_OBJECT->notification_title}"
					placeholder="{vtranslate('LBL_PM_WF_NOTIFY_TITLE_PH', 'PromotionalMaterial')}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_ADD_FIELD',$QUALIFIED_MODULE)}">
					<option></option>
					{$ALL_FIELD_OPTIONS}
				</select>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_PM_WF_NOTIFY_MESSAGE', 'PromotionalMaterial')}
			</div>
			<div class="col-sm-6 col-xs-9">
				<textarea name="notification_message" class="inputElement" rows="4" style="width:100%;">{$TASK_OBJECT->notification_message}</textarea>
				<span class="help-block">{vtranslate('LBL_PM_WF_NOTIFY_MESSAGE_HELP', 'PromotionalMaterial')}</span>
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_GENERAL_FIELDS',$QUALIFIED_MODULE)}">
					<option></option>
					{foreach from=$META_VARIABLES item=META_VARIABLE_KEY key=META_VARIABLE_VALUE}
						<option value="{if strpos(strtolower($META_VARIABLE_VALUE), 'url') === false}${/if}{$META_VARIABLE_KEY}">{vtranslate($META_VARIABLE_VALUE,$QUALIFIED_MODULE)}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function () {
	var scope = jQuery('select[name="notify_scope"]');
	function pmToggleUserIds() {
		if (scope.val() === 'user_ids') {
			jQuery('#pm_wf_notify_user_ids_row').show();
		} else {
			jQuery('#pm_wf_notify_user_ids_row').hide();
		}
	}
	scope.on('change', pmToggleUserIds);
	pmToggleUserIds();
});
</script>
{/strip}
