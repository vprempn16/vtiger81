{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="editViewPageDiv">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <form class="form-horizontal" id="EditView" name="EditView" method="post" action="index.php">
            <input type="hidden" name="module" value="Whatsapp" />
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="action" value="ActionAjax" />
            <input type="hidden" name="mode" value="save" />
            <input type="hidden" name="record" value="{$RECORD_MODEL->getId()}" />

            <div class="widget_header row-fluid">
                <div class="span12">
                    <h4>{if $RECORD_MODEL->getId()}{vtranslate('LBL_EDIT_CHANNEL', $QUALIFIED_MODULE)}{else}{vtranslate('LBL_ADD_CHANNEL', $QUALIFIED_MODULE)}{/if}</h4>
                </div>
            </div>

            <div class="contents">
                <table class="table table-bordered editViewContents">
                    <tbody>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_CHANNEL_NAME', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="name" value="{$RECORD_MODEL->getName()}" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_DESCRIPTION', $QUALIFIED_MODULE)}</td>
                            <td class="fieldValue">
                                <textarea class="inputElement" name="description">{$RECORD_MODEL->get('description')}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_APP_ID', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="app_id" value="{$RECORD_MODEL->get('app_id')}" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_APP_SECRET', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="password" class="inputElement" name="app_secret" value="{$RECORD_MODEL->get('app_secret')}" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_PHONE_NUMBER_ID', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="phone_number_id" value="{$RECORD_MODEL->get('phone_number_id')}" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_BUSINESS_ID', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="business_id" value="{$RECORD_MODEL->get('business_id')}" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_ACCESS_TOKEN', $QUALIFIED_MODULE)}<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <textarea class="inputElement" name="access_token" data-rule-required="true">{$RECORD_MODEL->get('access_token')}</textarea>
                                <p class="help-block"><small>{vtranslate('LBL_ACCESS_TOKEN_HELP', $QUALIFIED_MODULE)}</small></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">{vtranslate('LBL_IS_ACTIVE', $QUALIFIED_MODULE)}</td>
                            <td class="fieldValue">
                                <input type="checkbox" name="is_active" {if $RECORD_MODEL->get('is_active')}checked{/if} value="1" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
                    <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
                </div>
            </div>
        </form>
    </div>
</div>
