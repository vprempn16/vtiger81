<?php
/* Smarty version 4.3.2, created on 2026-03-11 14:08:38
  from '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/uitypes/SalutationDetailView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b17766b42b43_25896670',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '32130e0cda49d7bc052c40fc14f2838ccb462772' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Vtiger/uitypes/SalutationDetailView.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b17766b42b43_25896670 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['RECORD']->value->getDisplayValue('salutationtype');?>


<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);
}
}
