<?php
/* Smarty version 4.3.2, created on 2026-03-17 13:27:41
  from '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/ModuleSummaryView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b956cd38c309_46663057',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '75d18e67d96fc50143271480b7df30698d01b2ea' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/ModuleSummaryView.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b956cd38c309_46663057 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="recordDetails">
    <?php $_smarty_tpl->_subTemplateRender(vtemplate_path('DetailViewBlockView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['SUMMARY_RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0, true);
?>
</div><?php }
}
