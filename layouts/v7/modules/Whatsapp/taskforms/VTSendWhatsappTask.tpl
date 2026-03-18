{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="whatsapp-task-container">
        <div class="row form-group">
            <div class="col-sm-6 col-xs-6">
                <div class="row">
                    <div class="col-sm-3 col-xs-3 ">{vtranslate('Recipient', 'Whatsapp')}<span class="redColor">*</span></div>
                    <div class="col-sm-9 col-xs-9">
                        <select name="recepients" class="select2 wa-wf-recipient" data-validation-engine="validate[required]" style="width:100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                            {foreach from=$MODULE_MODEL->getFieldsByType('phone') item=FIELD_MODEL}
                                <option value="{$FIELD_MODEL->getName()}" {if $TASK_OBJECT->recepients eq $FIELD_MODEL->getName()}selected{/if}>
                                    {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)} ({$FIELD_MODEL->getName()})
                                </option>
                            {/foreach}
                        </select>
                        <div class="redColor" style="margin-top:5px; font-size:11px;">
                            *(Ensure the number includes the country code; otherwise, the message will fail)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-6 col-xs-6">
                <div class="row">
                    <div class="col-sm-3 col-xs-3 ">{vtranslate('WhatsApp Channel', 'Whatsapp')}<span class="redColor">*</span></div>
                    <div class="col-sm-9 col-xs-9">
                        <select name="whatsapp_channel" class="select2 wa-wf-channel" data-validation-engine="validate[required]" style="width:100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                            {foreach from=Settings_Whatsapp_Record_Model::getAllChannels() item=CHANNEL}
                                <option value="{$CHANNEL->getId()}" {if $TASK_OBJECT->whatsapp_channel eq $CHANNEL->getId()}selected{/if}>
                                    {$CHANNEL->get('name')} ({$CHANNEL->get('phone_number_id')})
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-6 col-xs-6">
                <div class="row">
                    <div class="col-sm-3 col-xs-3 ">{vtranslate('WhatsApp Template', 'Whatsapp')}<span class="redColor">*</span></div>
                    <div class="col-sm-9 col-xs-9">
                        <select name="templateid" class="select2 wa-wf-template" data-validation-engine="validate[required]" style="width:100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', 'Vtiger')}</option>
                            {if $TASK_OBJECT->templateid}
                                <option value="{$TASK_OBJECT->templateid}" selected>Loading...</option>
                            {/if}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {assign var=MAPPING_JSON value=$TASK_OBJECT->wa_mapping}
        {if is_array($MAPPING_JSON)}
            {assign var=MAPPING_JSON value=json_encode($MAPPING_JSON)}
        {/if}
        <input type="hidden" name="wa_mapping" class="wa-wf-mapping-data" value="{$MAPPING_JSON|escape:'html'}" />
        
        <div class="wa-wf-mapping-container col-sm-12" style="margin-top:20px; padding:0;">
            {* Dynamic Mapping UI Content *}
        </div>
    </div>
    <script type="text/javascript" src="layouts/v7/modules/Whatsapp/resources/VTSendWhatsappTask.js"></script>
{/strip}
