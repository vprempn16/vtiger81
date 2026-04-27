{* Leads-specific Owner field template.
   Uses pre-filtered FIELD_INFO picklist values and avoids core accessible-user
   masking that can hide hierarchy-allowed users. *}
{strip}
{assign var="FIELD_INFO" value=$FIELD_MODEL->getFieldInfo()}
{if $FIELD_MODEL->get('uitype') eq '53'}
	{if !empty($FILTERED_ASSIGNED_USERS)}
                {assign var=ALL_ACTIVEUSER_LIST value=$FILTERED_ASSIGNED_USERS}
        {else}
                {assign var=ALL_ACTIVEUSER_LIST value=$FIELD_INFO['picklistvalues'][vtranslate('LBL_USERS')]}
        {/if}
	{assign var=ALL_ACTIVEGROUP_LIST value=$FIELD_INFO['picklistvalues'][vtranslate('LBL_GROUPS')]}
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
	{assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
	{if $FIELD_VALUE eq ''}
		{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
	{/if}
	<select class="inputElement select2" type="owner" data-fieldtype="owner" data-fieldname="{$ASSIGNED_USER_ID}" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}"
		{if $FIELD_INFO["mandatory"] eq true} data-rule-required="true" {/if}
		{if php7_count($FIELD_INFO['validator'])}
			data-specific-rules='{ZEND_JSON::encode($FIELD_INFO["validator"])}'
		{/if}>
		{if $FIELD_MODEL->isCustomField() || $VIEW_SOURCE eq 'MASSEDIT'}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
		<optgroup label="{vtranslate('LBL_USERS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
				<option value="{$OWNER_ID}" data-picklistvalue='{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID && $VIEW_SOURCE neq 'MASSEDIT'} selected {/if} data-recordaccess=true data-userId="{$CURRENT_USER_ID}">
					{$OWNER_NAME}
				</option>
			{/foreach}
		</optgroup>
		{if !empty($ALL_ACTIVEGROUP_LIST)}
		<optgroup label="{vtranslate('LBL_GROUPS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEGROUP_LIST}
				<option value="{$OWNER_ID}" data-picklistvalue='{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-recordaccess=true>
					{$OWNER_NAME}
				</option>
			{/foreach}
		</optgroup>
		{/if}
	</select>
{/if}
{/strip}
