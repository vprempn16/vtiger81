{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="row">
        <div class="row form-group">
            <div class="col-sm-3 col-xs-3 control-label">{vtranslate('Action Title', $MODULE)}<span class="redColor">*</span></div>
            <div class="col-sm-9 col-xs-9">
                <input name="task_title" class="inputElement" value="{$TASK_OBJECT->task_title}" data-validation-engine="validate[required]" type="text" />
            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-3 col-xs-3 control-label">{vtranslate('Recipient', $MODULE)}<span class="redColor">*</span></div>
            <div class="col-sm-9 col-xs-9">
                <select name="recepients" class="select2" data-validation-engine="validate[required]" style="width:100%;">
                    <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                    {foreach from=$MODULE_MODEL->getFieldsByType('phone') item=FIELD_MODEL}
                        <option value="{$FIELD_MODEL->getName()}" {if $TASK_OBJECT->recepients eq $FIELD_MODEL->getName()}selected{/if}>
                            {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)} ({$FIELD_MODEL->getName()})
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-3 col-xs-3 control-label">{vtranslate('WhatsApp Channel', $MODULE)}<span class="redColor">*</span></div>
            <div class="col-sm-9 col-xs-9">
                <select name="whatsapp_channel" id="whatsapp_wf_channel" class="select2" data-validation-engine="validate[required]" style="width:100%;">
                    <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                    {foreach from=Settings_Whatsapp_Record_Model::getAllChannels() item=CHANNEL}
                        <option value="{$CHANNEL->getId()}" {if $TASK_OBJECT->whatsapp_channel eq $CHANNEL->getId()}selected{/if}>
                            {$CHANNEL->get('name')} ({$CHANNEL->get('phone_number_id')})
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-3 col-xs-3 control-label">{vtranslate('WhatsApp Template', $MODULE)}<span class="redColor">*</span></div>
            <div class="col-sm-9 col-xs-9">
                <select name="templateid" id="whatsapp_wf_template" class="select2" data-validation-engine="validate[required]" style="width:100%;">
                    <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                    {if $TASK_OBJECT->templateid}
                        {* Templates will be loaded via JS, but we can pre-populate if ID is present *}
                        <option value="{$TASK_OBJECT->templateid}" selected>Loading...</option>
                    {/if}
                </select>
            </div>
        </div>

        <input type="hidden" name="wa_mapping" id="whatsapp_wf_mapping_data" value='{$TASK_OBJECT->wa_mapping}' />
        
        <div id="whatsapp_wf_mapping_container" class="col-sm-12" style="margin-top:20px;">
            {* Dynamic Mapping UI Content *}
        </div>
    </div>
    <script type="text/javascript" src="layouts/v7/modules/com_vtiger_workflow/resources/VTSendWhatsappTask.js"></script>
{/strip}
