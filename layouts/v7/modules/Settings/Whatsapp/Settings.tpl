{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="listViewPageDiv" id="listViewContainer" style="padding: 20px;">
    <div class="listViewTopMenuDiv" style="margin-bottom: 20px;">
        <div class="row-fluid">
            <div class="span6">
                <h4 style="margin-top: 10px;">{vtranslate('LBL_WHATSAPP_CHANNELS', $QUALIFIED_MODULE)}</h4>
            </div>
            <div class="span6">
                <div class="btn-group listViewActions pull-right">
                    <button class="btn btn-default" id="addButton" onclick='window.location.href="{$MODULE_MODEL->getCreateRecordUrl()}"'>
                        <i class="fa fa-plus"></i>&nbsp;<strong>{vtranslate('LBL_ADD_CHANNEL', $QUALIFIED_MODULE)}</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="listViewContentDiv" id="listViewContents" style="margin-top: 20px;">
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width="20%">{vtranslate('LBL_CHANNEL_NAME', $QUALIFIED_MODULE)}</th>
                    <th width="30%">{vtranslate('LBL_APP_ID', $QUALIFIED_MODULE)}</th>
                    <th width="20%">{vtranslate('LBL_PHONE_NUMBER_ID', $QUALIFIED_MODULE)}</th>
                    <th width="15%">{vtranslate('LBL_STATUS', $QUALIFIED_MODULE)}</th>
                    <th width="15%" style="text-align: center;">{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$RECORDS item=RECORD}
                    <tr class="listViewEntries" data-id="{$RECORD->getId()}">
                        <td>{$RECORD->getName()}</td>
                        <td>{$RECORD->get('app_id')}</td>
                        <td>{$RECORD->get('phone_number_id')}</td>
                        <td>
                            {if $RECORD->get('is_active')}
                                <span class="label label-success">{vtranslate('LBL_ACTIVE', $QUALIFIED_MODULE)}</span>
                            {else}
                                <span class="label label-important">{vtranslate('LBL_INACTIVE', $QUALIFIED_MODULE)}</span>
                            {/if}
                        </td>
                        <td style="text-align: center;">
                            <div class="actions">
                                {foreach from=$RECORD->getRecordLinks() item=LINK}
                                    <a {if $LINK->get('linkurl')} href='{$LINK->get('linkurl')}' {/if} title="{vtranslate($LINK->get('linklabel'), $QUALIFIED_MODULE)}">
                                        <i class="{$LINK->get('linkicon')}"></i>
                                    </a>&nbsp;&nbsp;
                                {/foreach}
                            </div>
                        </td>
                    </tr>
                {foreachelse}
                    <tr class="emptyRecordsDiv">
                        <td colspan="5" class="emptyRecordsMessage">
                            {vtranslate('LBL_NO_CHANNELS_FOUND', $QUALIFIED_MODULE)}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
</div> {* Closing settingsIndexRaw from PreProcess if not using Raw view *}
