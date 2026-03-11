{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="modal-dialog">
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
                    {if !empty($VARIABLES)}
                        <h4>{vtranslate('LBL_TEMPLATE_VARIABLES', $QUALIFIED_MODULE)}</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{vtranslate('LBL_VARIABLE', $QUALIFIED_MODULE)}</th>
                                    <th>{vtranslate('LBL_ATTRIBUTE', $QUALIFIED_MODULE)}</th>
                                    <th>{vtranslate('LBL_CRM_FIELD', $QUALIFIED_MODULE)}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$VARIABLES item=VARIABLE}
                                    {assign var=VAR_KEY value=$VARIABLE.name}
                                    <tr>
                                        <td>{$VARIABLE.name}</td>
                                        <td>{$VARIABLE.type}</td>
                                        <td>
                                            <select class="select2 crm-field-select" name="mapping[{$VARIABLE.type}][{$VARIABLE.name}]" style="width: 100%;">
                                                <option value="">{vtranslate('LBL_SELECT_FIELD', $QUALIFIED_MODULE)}</option>
                                                {if !empty($MODULE_FIELDS)}
                                                    {foreach item=FIELD_LABEL key=FIELD_NAME from=$MODULE_FIELDS}
                                                        <option value="{$FIELD_NAME}" {if isset($MAPPINGS[$VAR_KEY]) && $MAPPINGS[$VAR_KEY]['crm_field'] eq $FIELD_NAME}selected{/if}>{$FIELD_LABEL}</option>
                                                    {/foreach}
                                                {/if}
                                            </select>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
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
