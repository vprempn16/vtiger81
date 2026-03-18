<?php
/* Smarty version 4.3.2, created on 2026-03-17 08:36:17
  from '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/taskforms/VTWhatsappMapping.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b91281e15859_41677338',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a77d25893947674f02a4185adaf1a4d077463cb0' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Whatsapp/taskforms/VTWhatsappMapping.tpl',
      1 => 1773400586,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b91281e15859_41677338 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="col-sm-12" style="padding: 0;"><?php if ($_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['HEADER']) {?><h5 class="m-b-10"><strong>Header</strong></h5><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['HEADER'], 'P');
$_smarty_tpl->tpl_vars['P']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['P']->value) {
$_smarty_tpl->tpl_vars['P']->do_else = false;
?><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><label class="col-sm-5 col-xs-5"><?php echo $_smarty_tpl->tpl_vars['P']->value['var'];?>
 &rarr; <?php echo $_smarty_tpl->tpl_vars['P']->value['example'];?>
</label><div class="col-sm-7 col-xs-7"><select class="select2 wa-wf-map" data-comp="HEADER" data-var="<?php echo $_smarty_tpl->tpl_vars['P']->value['var'];?>
" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULE_FIELDS']->value, 'FIELD');
$_smarty_tpl->tpl_vars['FIELD']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD']->value) {
$_smarty_tpl->tpl_vars['FIELD']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['FIELD']->value['label'];?>
 (<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div></div></div><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
if ($_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['BODY']) {?><h5 class="m-b-10"><strong>Body</strong></h5><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['BODY'], 'P');
$_smarty_tpl->tpl_vars['P']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['P']->value) {
$_smarty_tpl->tpl_vars['P']->do_else = false;
?><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><label class="col-sm-5 col-xs-5"><?php echo $_smarty_tpl->tpl_vars['P']->value['var'];?>
 &rarr; <?php echo $_smarty_tpl->tpl_vars['P']->value['example'];?>
</label><div class="col-sm-7 col-xs-7"><select class="select2 wa-wf-map" data-comp="BODY" data-var="<?php echo $_smarty_tpl->tpl_vars['P']->value['var'];?>
" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULE_FIELDS']->value, 'FIELD');
$_smarty_tpl->tpl_vars['FIELD']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD']->value) {
$_smarty_tpl->tpl_vars['FIELD']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['FIELD']->value['label'];?>
 (<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div></div></div><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
if ($_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['BUTTONS']) {?><h5 class="m-b-10"><strong>Buttons</strong></h5><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['PLACEHOLDERS']->value['BUTTONS'], 'BUTTON', false, 'INDEX');
$_smarty_tpl->tpl_vars['BUTTON']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['INDEX']->value => $_smarty_tpl->tpl_vars['BUTTON']->value) {
$_smarty_tpl->tpl_vars['BUTTON']->do_else = false;
?><div class="m-b-15"><div class="m-b-5"><strong><?php echo $_smarty_tpl->tpl_vars['INDEX']->value;?>
 &rarr; <?php echo $_smarty_tpl->tpl_vars['BUTTON']->value['label'];?>
</strong></div><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['BUTTON']->value['vars'], 'P');
$_smarty_tpl->tpl_vars['P']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['P']->value) {
$_smarty_tpl->tpl_vars['P']->do_else = false;
?><div class="row form-group"><div class="col-sm-6 col-xs-6"><div class="row"><label class="col-sm-5 col-xs-5">Button <?php echo $_smarty_tpl->tpl_vars['INDEX']->value;?>
 URL Parameter</label><div class="col-sm-7 col-xs-7"><select class="select2 wa-wf-map" data-comp="BUTTONS_<?php echo $_smarty_tpl->tpl_vars['INDEX']->value;?>
" data-var="<?php echo $_smarty_tpl->tpl_vars['P']->value['var'];?>
" style="width:100%;"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION','Vtiger');?>
</option><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['MODULE_FIELDS']->value, 'FIELD');
$_smarty_tpl->tpl_vars['FIELD']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['FIELD']->value) {
$_smarty_tpl->tpl_vars['FIELD']->do_else = false;
?><option value="<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['FIELD']->value['label'];?>
 (<?php echo $_smarty_tpl->tpl_vars['FIELD']->value['name'];?>
)</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div></div></div><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></div><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?><hr><h5><strong>Template Preview</strong></h5><div class="preview-box well" style="background:#fdfdfd; min-height:60px; border: 1px solid #eee; padding:15px; border-radius:4px; margin-bottom:20px;"><?php echo $_smarty_tpl->tpl_vars['PREVIEW_HTML']->value;?>
</div></div>
<?php }
}
