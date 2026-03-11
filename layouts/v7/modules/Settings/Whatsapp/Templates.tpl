{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="listViewPageDiv" id="listViewContainer" style="padding: 20px;">
    <div class="listViewTopMenuDiv">
        <div class="listViewActionsContainer clearfix">
            <div class="btn-group listViewActions pull-left">
                <select class="select2" id="channelFilter" style="width: 250px;">
                    {foreach from=$CHANNELS item=CHANNEL}
                        <option value="{$CHANNEL->getId()}" {if $SELECTED_CHANNEL_ID eq $CHANNEL->getId()}selected{/if}>{$CHANNEL->getName()}</option>
                    {/foreach}
                </select>
            </div>
            {if $SELECTED_CHANNEL_ID}
            <div class="btn-group listViewActions pull-right">
                <button class="btn btn-primary" id="syncTemplates" data-channel-id="{$SELECTED_CHANNEL_ID}">
                    <i class="fa fa-refresh"></i>&nbsp;<strong>{vtranslate('LBL_SYNC_TEMPLATES', $QUALIFIED_MODULE)}</strong>
                </button>
            </div>
            {/if}
        </div>
    </div>
    <div class="listViewContentDiv" id="listViewContents" style="margin-top: 20px;">
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width="30%">{vtranslate('LBL_TEMPLATE_NAME', $QUALIFIED_MODULE)}</th>
                    <th width="20%">{vtranslate('LBL_CATEGORY', $QUALIFIED_MODULE)}</th>
                    <th width="15%">{vtranslate('LBL_LANGUAGE', $QUALIFIED_MODULE)}</th>
                    <th width="15%">{vtranslate('LBL_STATUS', $QUALIFIED_MODULE)}</th>
                    <th width="20%" style="text-align: center;">{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$TEMPLATES item=TEMPLATE}
                    <tr class="listViewEntries" data-id="{$TEMPLATE->getId()}">
                        <td>{$TEMPLATE->get('template_name')}</td>
                        <td>{$TEMPLATE->get('category')}</td>
                        <td>{$TEMPLATE->get('language')}</td>
                        <td>
                            <span class="label {if $TEMPLATE->get('status') eq 'APPROVED'}label-success{else}label-info{/if}">
                                {$TEMPLATE->get('status')}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="javascript:void(0);" class="templateMapping" data-id="{$TEMPLATE->getId()}" title="{vtranslate('LBL_MAPPING', $QUALIFIED_MODULE)}">
                                <i class="fa fa-exchange" style="font-size: 16px;"></i>
                            </a>
                        </td>
                    </tr>
                {foreachelse}
                    <tr class="emptyRecordsDiv">
                        <td colspan="5" class="emptyRecordsMessage" style="text-align: center; padding: 50px;">
                            <p>{vtranslate('LBL_NO_TEMPLATES_FOUND', $QUALIFIED_MODULE)}</p>
                            <br/>
                            <button class="btn btn-primary btn-lg" id="syncTemplatesCenter" data-channel-id="{$SELECTED_CHANNEL_ID}">
                                <i class="fa fa-refresh"></i>&nbsp;<strong>{vtranslate('LBL_SYNC_TEMPLATES', $QUALIFIED_MODULE)}</strong>
                            </button>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
</div>
