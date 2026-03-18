{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="modal-title">{vtranslate('LBL_MAP_TEMPLATE', $QUALIFIED_MODULE)}: {$TEMPLATE_NAME}</h3>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <form class="form-horizontal" id="mappingForm">
                <input type="hidden" name="template_id" value="{$TEMPLATE_ID}" />
                <input type="hidden" name="meta_template_id" value="{$META_TEMPLATE_ID}" />
                <input type="hidden" name="template_language" value="{$TEMPLATE_LANGUAGE}" />
                
                <div class="form-group">
                    <label class="control-label col-lg-3">{vtranslate('LBL_SELECT_MODULE', $QUALIFIED_MODULE)}</label>
                    <div class="col-lg-8">
                        <select class="select2" name="crm_module" id="crm_module" style="width: 100%;">
                            <option value="">{vtranslate('LBL_SELECT_OPTION', $QUALIFIED_MODULE)}</option>
                            {foreach item=MODULE_MODEL key=TAB_ID from=$MODULES}
                                <option value="{$MODULE_MODEL->getName()}" {if $MAPPED_MODULE eq $MODULE_MODEL->getName()}selected{/if}>
                                    {vtranslate($MODULE_MODEL->getName(), $MODULE_MODEL->getName())}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <hr />

                <div id="mappingContainer">
                    {if !empty($GROUPED_VARIABLES)}
                        {foreach key=GROUP_TYPE item=GROUP_VARS from=$GROUPED_VARIABLES}
                            <h4 style="margin-top: 20px; margin-bottom: 15px;"><strong>{ucfirst(strtolower($GROUP_TYPE))}</strong></h4>
                            
                            {foreach item=VARIABLE from=$GROUP_VARS}
                                {assign var=VAR_NAME value=$VARIABLE.name}
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <label class="control-label col-lg-3" style="text-align: left; padding-top: 6px;">
                                        {if $VARIABLE.example}
                                            {$VARIABLE.key} &rarr; {$VARIABLE.example}
                                        {else}
                                            {$VARIABLE.key}
                                        {/if}
                                        {if $VARIABLE.context}
                                            <div style="font-weight: normal; font-size: 11px; margin-top: 3px;">{$VARIABLE.context}</div>
                                        {/if}
                                    </label>
                                    <div class="col-lg-8" style="padding-top: 3px;">
                                            <select class="select2 crm-field-select" name="mapping[{$VARIABLE.type}][{$VARIABLE.name}]" style="width: 100%;">
                                                <option value="">{vtranslate('LBL_SELECT_OPTION', $QUALIFIED_MODULE)}</option>
                                                {if !empty($MODULE_FIELDS)}
                                                    {assign var=MAPPING_KEY value=$VARIABLE.type|cat:'_'|cat:$VAR_NAME}
                                                    {foreach item=FIELD_LABEL key=FIELD_NAME from=$MODULE_FIELDS}
                                                        <option value="{$FIELD_NAME}" {if isset($MAPPINGS[$MAPPING_KEY]) && $MAPPINGS[$MAPPING_KEY]['crm_field'] eq $FIELD_NAME}selected{/if}>{$FIELD_LABEL}</option>
                                                    {/foreach}
                                                {/if}
                                            </select>
                                    </div>
                                </div>
                            {/foreach}
                        {/foreach}

                        <hr style="margin-top: 30px; margin-bottom: 20px;" />
                        
                        <h4 style="margin-bottom: 15px;">Template Preview</h4>
                        <div style="background-color: #fafbfc; border: 1px solid #e1e4e8; padding: 20px; border-radius: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color: #24292e; max-width: 100%;">
                            {foreach item=COMP from=$COMPONENTS}
                                {if $COMP.type eq 'HEADER'}
                                    {if $COMP.format eq 'TEXT'}
                                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 10px;">{$COMP.text}</div>
                                    {elseif $COMP.format eq 'IMAGE'}
                                        <div style="background: #e1e4e8; height: 120px; width: 100%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; border-radius: 4px; color: #586069;">[ IMAGE HEADER ]</div>
                                    {elseif $COMP.format eq 'DOCUMENT'}
                                        <div style="background: #e1e4e8; padding: 12px; margin-bottom: 15px; border-radius: 4px; color: #586069;">&#128196; [ DOCUMENT HEADER ]</div>
                                    {elseif $COMP.format eq 'VIDEO'}
                                        <div style="background: #e1e4e8; padding: 12px; margin-bottom: 15px; border-radius: 4px; color: #586069;">&#127909; [ VIDEO HEADER ]</div>
                                    {/if}
                                {elseif $COMP.type eq 'BODY'}
                                    <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">{$COMP.text}</div>
                                {elseif $COMP.type eq 'FOOTER'}
                                    <div style="color: #586069; font-size: 13px; margin-top: 10px;">{$COMP.text}</div>
                                {elseif $COMP.type eq 'BUTTONS'}
                                    <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 8px;">
                                    {foreach item=BTN from=$COMP.buttons}
                                        <div style="background-color: #ffffff; border: 1px solid #e1e4e8; color: #0366d6; text-align: center; padding: 8px 16px; border-radius: 4px; font-size: 14px; display: inline-block; cursor: default;">
                                            {if $BTN.type eq 'URL'}
                                                <i class="fa fa-external-link"></i>
                                            {elseif $BTN.type eq 'PHONE_NUMBER'}
                                                <i class="fa fa-phone"></i>
                                            {elseif $BTN.type eq 'QUICK_REPLY'}
                                                <i class="fa fa-reply"></i>
                                            {/if}
                                            {$BTN.text}
                                        </div>
                                    {/foreach}
                                    </div>
                                {/if}
                            {/foreach}
                        </div>

                    {else}
                        <div class="alert alert-info">
                            {vtranslate('LBL_NO_VARIABLES_FOUND', $QUALIFIED_MODULE)}
                        </div>
                    {/if}
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</button>
            <button type="button" class="btn btn-primary" id="saveMappingBtn">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
        </div>
    </div>
</div>
