<?php
/* Smarty version 4.3.2, created on 2025-09-06 11:21:48
  from '/var/www/html/vtiger81/layouts/v7/modules/Inventory/PopupContents.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_68bc194c851052_61993455',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d4b168739655e777000904449a2bafeab0765b7' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Inventory/PopupContents.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68bc194c851052_61993455 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender(vtemplate_path("PicklistColorMap.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?><div class="row"><?php $_smarty_tpl->_subTemplateRender(vtemplate_path('PopupNavigation.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?></div><div id='popupContentsDiv'><div class="row"><div class="col-md-12"><?php $_smarty_tpl->_subTemplateRender(vtemplate_path("PopupEntries.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?></div></div></div>

<?php }
}
