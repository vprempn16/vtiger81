<?php
/* Smarty version 4.3.2, created on 2026-03-11 14:08:46
  from '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/ComposeWhatsappModal.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b1776ebc3da6_07128604',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '93190d1833a779071459493b2913fb988d56e4b1' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/ComposeWhatsappModal.tpl',
      1 => 1773237372,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b1776ebc3da6_07128604 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="SendWhatsappForm modal-dialog modal-lg" id="composeWhatsappContainer"><div class="modal-content"><form class="form-horizontal" id="massWhatsappForm" method="post" action="index.php" enctype="multipart/form-data" name="massWhatsappForm"><?php ob_start();
echo vtranslate('Send WhatsApp Message',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->_subTemplateRender(vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('TITLE'=>$_prefixVariable1), 0, true);
?><div class="modal-body"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
"/><input type="hidden" name="action" value="MassActionAjax" /><input type="hidden" name="mode" value="sendWhatsappMessage" /><input type="hidden" name="source_module" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" /><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value;?>
" /><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('To Number',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <span class="redColor">*</span></label><div class="col-sm-5"><select class="select2 form-control" name="to_number" id="whatsappToNumber" data-rule-required="true"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['PHONE_FIELDS']->value, 'PHONE_LABEL', false, 'FIELD_NAME');
$_smarty_tpl->tpl_vars['PHONE_LABEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_NAME']->value => $_smarty_tpl->tpl_vars['PHONE_LABEL']->value) {
$_smarty_tpl->tpl_vars['PHONE_LABEL']->do_else = false;
$_smarty_tpl->_assignInScope('RECORD_PHONE', $_smarty_tpl->tpl_vars['RECORD_PHONE_NUMBERS']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]);
if (!empty($_smarty_tpl->tpl_vars['RECORD_PHONE']->value)) {?><option value="<?php echo $_smarty_tpl->tpl_vars['RECORD_PHONE']->value;?>
" selected><?php echo $_smarty_tpl->tpl_vars['PHONE_LABEL']->value;?>
: <?php echo $_smarty_tpl->tpl_vars['RECORD_PHONE']->value;?>
</option><?php } else { ?><option value=""><?php echo $_smarty_tpl->tpl_vars['PHONE_LABEL']->value;?>
: <?php echo vtranslate('LBL_NOT_FOUND',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('From Channel',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <span class="redColor">*</span></label><div class="col-sm-5"><select class="select2 form-control" name="channel_id" id="whatsappChannel" data-rule-required="true"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['CHANNELS']->value, 'CHANNEL');
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['CHANNEL']->value) {
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value['phone'];?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('WhatsApp Template',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="col-sm-5"><select class="select2 form-control" name="template_id" id="whatsappTemplate"><option value="none"><?php echo vtranslate('None - Type Message',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option></select></div><div class="col-sm-4"><span class="help-block"><small><i class="fa fa-info-circle"></i> Showing templates mapped to <?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
</small></span></div></div><hr><div id="whatsappFreeFormContainer"><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('Media / Attachment',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="col-sm-8"><input type="file" name="whatsapp_media" id="whatsappMedia" class="form-control" /><span class="help-block"><small>Images, Documents, Video</small></span></div></div><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('Message',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <span class="redColor">*</span></label><div class="col-sm-8"><textarea name="message_text" id="whatsappMessageText" class="form-control" rows="5" placeholder="<?php echo vtranslate('Type your message here...',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></textarea></div></div></div><div id="whatsappTemplatePreviewContainer" style="display: none;"><div class="row form-group"><label class="col-sm-3 control-label"><?php echo vtranslate('Message Preview',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="col-sm-8"><div class="well well-sm" id="whatsappTemplatePreviewBox" style="background: #fdfdfd; min-height: 100px; white-space: pre-wrap; word-wrap: break-word;"><div class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Loading Preview...</div></div><span class="help-block text-warning"><small><i class="fa fa-exclamation-triangle"></i> Templates cannot be edited manually. CRM values are substituted automatically.</small></span></div></div></div></div><div class="modal-footer"><div class="pull-right cancelLinkContainer"><a href="#" class="cancelLink btn btn-link" data-dismiss="modal"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div><button id="sendWhatsappBtnSubmit" name="sendWhatsapp" class="btn btn-success" type="submit"><strong><i class="fa fa-paper-plane"></i> <span id="sendBtnLabel"><?php echo vtranslate('Send Message',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></strong></button></div></form></div></div>
<?php }
}
