<?php
/* Smarty version 4.3.2, created on 2026-03-17 08:35:00
  from '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/taskforms/VTSendWhatsappTask.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b91234da0ed6_22359598',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e2b3288c24d9303056ae7f78e238dfd3cc3cf4b' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/taskforms/VTSendWhatsappTask.tpl',
      1 => 1773477447,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b91234da0ed6_22359598 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="whatsapp-task-container"><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><div class="col-sm-3 col-xs-3 "><?php echo vtranslate('Recipient','Whatsapp');?>
<span class="redColor">*</span></div><div class="col-sm-9 col-xs-9"><select name="recepients" class="select2 wa-wf-recipient" data-validation-engine="validate[required]" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getFieldsByType('phone'), 'FIELD_MODEL');
$_smarty_tpl->tpl_vars['FIELD_MODEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->value) {
$_smarty_tpl->tpl_vars['FIELD_MODEL']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" <?php if ($_smarty_tpl->tpl_vars['TASK_OBJECT']->value->recepients == $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName()) {?>selected<?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
 (<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select><div class="redColor" style="margin-top:5px; font-size:11px;">*(Ensure the number includes the country code; otherwise, the message will fail)</div></div></div></div></div><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><div class="col-sm-3 col-xs-3 "><?php echo vtranslate('WhatsApp Channel','Whatsapp');?>
<span class="redColor">*</span></div><div class="col-sm-9 col-xs-9"><select name="whatsapp_channel" class="select2 wa-wf-channel" data-validation-engine="validate[required]" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, Settings_Whatsapp_Record_Model::getAllChannels(), 'CHANNEL');
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['CHANNEL']->value) {
$_smarty_tpl->tpl_vars['CHANNEL']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['TASK_OBJECT']->value->whatsapp_channel == $_smarty_tpl->tpl_vars['CHANNEL']->value->getId()) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value->get('name');?>
 (<?php echo $_smarty_tpl->tpl_vars['CHANNEL']->value->get('phone_number_id');?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div></div></div><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><div class="col-sm-3 col-xs-3 "><?php echo vtranslate('WhatsApp Template','Whatsapp');?>
<span class="redColor">*</span></div><div class="col-sm-9 col-xs-9"><select name="templateid" class="select2 wa-wf-template" data-validation-engine="validate[required]" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php if ($_smarty_tpl->tpl_vars['TASK_OBJECT']->value->templateid) {?><option value="<?php echo $_smarty_tpl->tpl_vars['TASK_OBJECT']->value->templateid;?>
" selected>Loading...</option><?php }?></select></div></div></div></div><?php $_smarty_tpl->_assignInScope('MAPPING_JSON', $_smarty_tpl->tpl_vars['TASK_OBJECT']->value->wa_mapping);
if (is_array($_smarty_tpl->tpl_vars['MAPPING_JSON']->value)) {
$_smarty_tpl->_assignInScope('MAPPING_JSON', json_encode($_smarty_tpl->tpl_vars['MAPPING_JSON']->value));
}?><input type="hidden" name="wa_mapping" class="wa-wf-mapping-data" value="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['MAPPING_JSON']->value, ENT_QUOTES, 'UTF-8', true);?>
" /><div class="wa-wf-mapping-container col-sm-12" style="margin-top:20px; padding:0;"></div></div><?php echo '<script'; ?>
 type="text/javascript" src="layouts/v7/modules/Whatsapp/resources/VTSendWhatsappTask.js"><?php echo '</script'; ?>
>
<?php }
}
