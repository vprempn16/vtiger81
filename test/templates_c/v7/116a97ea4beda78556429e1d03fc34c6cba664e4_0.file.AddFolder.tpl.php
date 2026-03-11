<?php
/* Smarty version 4.3.2, created on 2026-03-11 16:02:10
  from '/var/www/html/vtiger81/layouts/v7/modules/Documents/AddFolder.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b192026ff278_90312202',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '116a97ea4beda78556429e1d03fc34c6cba664e4' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Documents/AddFolder.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b192026ff278_90312202 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="modal-dialog modelContainer"><div class = "modal-content"><?php ob_start();
echo vtranslate('LBL_ADD_NEW_FOLDER',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_assignInScope('HEADER_TITLE', $_prefixVariable1);
if ($_smarty_tpl->tpl_vars['FOLDER_ID']->value) {
ob_start();
echo vtranslate('LBL_EDIT_FOLDER',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable2=ob_get_clean();
$_smarty_tpl->_assignInScope('HEADER_TITLE', $_prefixVariable2.": ".((string)$_smarty_tpl->tpl_vars['FOLDER_NAME']->value));
}
$_smarty_tpl->_subTemplateRender(vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('TITLE'=>$_smarty_tpl->tpl_vars['HEADER_TITLE']->value), 0, true);
?><form class="form-horizontal" id="addDocumentsFolder" method="post" action="index.php"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" /><input type="hidden" name="action" value="Folder" /><input type="hidden" name="mode" value="save" /><?php if ($_smarty_tpl->tpl_vars['FOLDER_ID']->value != null) {?><input type="hidden" name="folderid" value="<?php echo $_smarty_tpl->tpl_vars['FOLDER_ID']->value;?>
" /><input type="hidden" name="savemode" value="<?php echo $_smarty_tpl->tpl_vars['SAVE_MODE']->value;?>
" /><?php }?><div class="modal-body"><div class="container-fluid"><div class="form-group"><label class="control-label fieldLabel col-sm-3"><span class="redColor">*</span><?php echo vtranslate('LBL_FOLDER_NAME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="controls col-sm-9"><input class="inputElement" id="documentsFolderName" data-rule-required="true" name="foldername" type="text" value="<?php if ($_smarty_tpl->tpl_vars['FOLDER_NAME']->value != null) {
echo $_smarty_tpl->tpl_vars['FOLDER_NAME']->value;
}?>"/></div></div><div class="form-group"><label class="control-label fieldLabel col-sm-3"><?php echo vtranslate('LBL_FOLDER_DESCRIPTION',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="controls col-sm-9"><textarea rows="3" class="inputElement form-control" name="folderdesc" id="description" style="resize: vertical;"><?php if ($_smarty_tpl->tpl_vars['FOLDER_DESC']->value != null) {
echo $_smarty_tpl->tpl_vars['FOLDER_DESC']->value;
}?></textarea></div></div></div></div><?php $_smarty_tpl->_subTemplateRender(vtemplate_path('ModalFooter.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?></form></div></div>

<?php }
}
