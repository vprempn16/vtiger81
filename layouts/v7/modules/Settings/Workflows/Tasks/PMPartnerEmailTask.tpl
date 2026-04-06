{*+**********************************************************************************
 * Workflow task: send email using EmailTemplates row + merge fields.
 *************************************************************************************}
{strip}
<div class="row" id="PMPartnerEmailTaskContainer">
	<div class="col-sm-12 col-xs-12">
		<p class="help-block">
			{vtranslate('LBL_PM_WF_EMAIL_HELP', 'PromotionalMaterial')}
		</p>

		{if $EMAIL_TEMPLATES}
		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_EMAIL_TEMPLATES','EmailTemplates')}<span class="redColor">*</span>
			</div>
			<div class="col-sm-9 col-xs-9">
				<select name="emailTemplateId" class="select2" style="min-width:320px;" data-rule-required="true">
					<option value="">{vtranslate('LBL_SELECT_OPTION', $QUALIFIED_MODULE)}</option>
					{foreach from=$EMAIL_TEMPLATES item=EMAIL_TEMPLATE}
						{if !$EMAIL_TEMPLATE->isDeleted()}
							<option value="{$EMAIL_TEMPLATE->getId()}" {if $TASK_OBJECT->emailTemplateId eq $EMAIL_TEMPLATE->getId()}selected{/if}>
								{$EMAIL_TEMPLATE->get('templatename')}
							</option>
						{/if}
					{/foreach}
				</select>
			</div>
		</div>
		{else}
		<div class="alert alert-warning">
			{vtranslate('LBL_PM_WF_EMAIL_NO_TEMPLATES', 'PromotionalMaterial')}
		</div>
		{/if}

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">
				{vtranslate('LBL_TO',$QUALIFIED_MODULE)}<span class="redColor">*</span>
			</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="recepient" class="fields inputElement" data-rule-required="true" style="width:100%;"
					value="{$TASK_OBJECT->recepient}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}">
					<option></option>
					{$EMAIL_FIELD_OPTION}
				</select>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">{vtranslate('LBL_FROM', $QUALIFIED_MODULE)}</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="fromEmail" class="fields inputElement" style="width:100%;"
					value="{$TASK_OBJECT->fromEmail}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}">
					<option></option>
					{$FROM_EMAIL_FIELD_OPTION}
				</select>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">{vtranslate('Reply To',$QUALIFIED_MODULE)}</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="replyTo" class="fields inputElement" style="width:100%;"
					value="{$TASK_OBJECT->replyTo}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}">
					<option></option>
					{$EMAIL_FIELD_OPTION}
				</select>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">{vtranslate('LBL_CC',$QUALIFIED_MODULE)}</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="emailcc" class="fields inputElement" style="width:100%;"
					value="{$TASK_OBJECT->emailcc}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}">
					<option></option>
					{$EMAIL_FIELD_OPTION}
				</select>
			</div>
		</div>

		<div class="row form-group">
			<div class="col-sm-3 col-xs-3 control-label">{vtranslate('LBL_BCC',$QUALIFIED_MODULE)}</div>
			<div class="col-sm-6 col-xs-9">
				<input type="text" name="emailbcc" class="fields inputElement" style="width:100%;"
					value="{$TASK_OBJECT->emailbcc}" />
			</div>
			<div class="col-sm-3 col-xs-12">
				<select class="task-fields select2" style="min-width:220px" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS',$QUALIFIED_MODULE)}">
					<option></option>
					{$EMAIL_FIELD_OPTION}
				</select>
			</div>
		</div>
	</div>
</div>
{/strip}
