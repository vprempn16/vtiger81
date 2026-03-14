{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="SendWhatsappForm modal-dialog modal-lg" id="composeWhatsappContainer">
        <div class="modal-content">
            <form class="form-horizontal" id="massWhatsappForm" method="post" action="index.php" enctype="multipart/form-data" name="massWhatsappForm">
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE="{vtranslate('Send WhatsApp Message', $MODULE)}"}
                
                <div class="modal-body">
                    <input type="hidden" name="module" value="{$MODULE}"/>
                    <input type="hidden" name="action" value="MassActionAjax" />
                    <input type="hidden" name="mode" value="sendWhatsappMessage" />
                    <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
                    <input type="hidden" name="record" value="{$RECORD}" />
                    
                    {* 1. To Number Selection *}
                    <div class="row form-group">
                        <label class="col-sm-3 control-label">{vtranslate('To Number', $MODULE)} <span class="redColor">*</span></label>
                        <div class="col-sm-5">
                            <select class="select2 form-control" name="to_number" id="whatsappToNumber" data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                                {foreach key=FIELD_NAME item=PHONE_LABEL from=$PHONE_FIELDS}
                                    {assign var=RECORD_PHONE value=$RECORD_PHONE_NUMBERS[$FIELD_NAME]}
                                    <option value="{$FIELD_NAME}" {if !empty($RECORD_PHONE)}selected{/if}>
                                        {$PHONE_LABEL}{if !empty($RECORD_PHONE)}: {$RECORD_PHONE}{/if}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    {* 2. Channel Selection *}
                    <div class="row form-group">
                        <label class="col-sm-3 control-label">{vtranslate('From Channel', $MODULE)} <span class="redColor">*</span></label>
                        <div class="col-sm-5">
                            <select class="select2 form-control" name="channel_id" id="whatsappChannel" data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                                {foreach item=CHANNEL from=$CHANNELS}
                                    <option value="{$CHANNEL['id']}">{$CHANNEL['name']} ({$CHANNEL['phone']})</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    {* 3. Template Selection (Dynamic) *}
                    <div class="row form-group">
                        <label class="col-sm-3 control-label">{vtranslate('WhatsApp Template', $MODULE)}</label>
                        <div class="col-sm-5">
                            <select class="select2 form-control" name="template_id" id="whatsappTemplate">
                                <option value="none">{vtranslate('None - Type Message', $MODULE)}</option>
                                {* Options will be populated via AJAX based on Channel & Source Module *}
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <span class="help-block"><small><i class="fa fa-info-circle"></i> Showing templates mapped to {$SOURCE_MODULE}</small></span>
                        </div>
                    </div>

                    <hr>

                    {* 4. Message Interface (Dynamic) *}
                    
                    {* --- State A: Default Free-form Text / Media --- *}
                    <div id="whatsappFreeFormContainer">
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{vtranslate('Media / Attachment', $MODULE)}</label>
                            <div class="col-sm-8">
                                <input type="file" name="whatsapp_media" id="whatsappMedia" class="form-control" accept=".jpg, .jpeg, .png, .webp, .pdf, .txt, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .mp4, .3gp, .aac, .mpeg, .amr, .ogg, .opus" />
                                <span class="help-block"><small>Images, Documents, Video</small></span>
                            </div>
                        </div>

                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{vtranslate('Message', $MODULE)} <span class="redColor">*</span></label>
                            <div class="col-sm-8">
                                <textarea name="message_text" id="whatsappMessageText" class="form-control" rows="5" placeholder="{vtranslate('Type your message here...', $MODULE)}"></textarea>
                            </div>
                        </div>
                    </div>

                    {* --- State B: Template Preview --- *}
                    <div id="whatsappTemplatePreviewContainer" style="display: none;">
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{vtranslate('Message Preview', $MODULE)}</label>
                            <div class="col-sm-8">
                                <div class="well well-sm" id="whatsappTemplatePreviewBox" style="background: #fdfdfd; min-height: 100px; white-space: pre-wrap; word-wrap: break-word;">
                                    <div class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Loading Preview...</div>
                                </div>
                                <span class="help-block text-warning"><small><i class="fa fa-exclamation-triangle"></i> Templates cannot be edited manually. CRM values are substituted automatically.</small></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer">
                        <a href="#" class="cancelLink btn btn-link" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button id="sendWhatsappBtnSubmit" name="sendWhatsapp" class="btn btn-success" type="submit">
                        <strong><i class="fa fa-paper-plane"></i> <span id="sendBtnLabel">{vtranslate('Send Message', $MODULE)}</span></strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
{/strip}
