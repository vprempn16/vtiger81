<?php
/* Smarty version 4.3.2, created on 2026-03-17 08:34:27
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/Workflows/TasksList.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b91213c793f2_83398664',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8d80880ec3666e24e43b04ceb6b9dcbb2f4f9dd7' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/Workflows/TasksList.tpl',
      1 => 1693558649,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b91213c793f2_83398664 (Smarty_Internal_Template $_smarty_tpl) {
?><div style="padding-left: 15px;"><div id="table-content" class="table-container"><table id="listview-table" class="table <?php if ($_smarty_tpl->tpl_vars['TASK_LIST']->value == '0') {?>listview-table-norecords <?php } else { ?> listview-table<?php }?> "><thead><tr class="listViewContentHeader"><th width="20%"><?php echo vtranslate('LBL_ACTIVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th><th width="30%"><?php echo vtranslate('LBL_TASK_TYPE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th><th><?php echo vtranslate('LBL_TASK_TITLE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th></tr></thead><tbody><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['TASK_LIST']->value, 'TASK');
$_smarty_tpl->tpl_vars['TASK']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['TASK']->value) {
$_smarty_tpl->tpl_vars['TASK']->do_else = false;
?><tr class="listViewEntries"><td><div class="pull-left actions"><span class="actionImages"><a data-url="<?php echo $_smarty_tpl->tpl_vars['TASK']->value->getEditViewUrl();?>
"><i class="fa fa-pencil alignMiddle" title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"></i></a>&nbsp;&nbsp;<a class="deleteTask" data-deleteurl="<?php echo $_smarty_tpl->tpl_vars['TASK']->value->getDeleteActionUrl();?>
"><i class="fa fa-trash alignMiddle" title="<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"></i></a></span></div>&nbsp;&nbsp;<input style="opacity: 0;" type="checkbox" data-on-color="success" class="taskStatus" data-statusurl="<?php echo $_smarty_tpl->tpl_vars['TASK']->value->getChangeStatusUrl();?>
" <?php if ($_smarty_tpl->tpl_vars['TASK']->value->isActive()) {?> checked="" value="on" <?php } else { ?> value="off" <?php }?> /></td><td class="listViewEntryValue"><?php echo vtranslate($_smarty_tpl->tpl_vars['TASK']->value->getTaskType()->getLabel(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</td><td><span class="pull-left"><?php echo Vtiger_Util_Helper::toSafeHTML($_smarty_tpl->tpl_vars['TASK']->value->getName());?>
</span></td><tr><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><tr class="listViewEntries hide taskTemplate"><td><div class="pull-left actions"><span class="actionImages"><a class="editTask"><i class="fa fa-pencil alignMiddle" ></i></a>&nbsp;&nbsp;<a class="deleteTaskTemplate"><i class="fa fa-trash alignMiddle"></i></a></span></div>&nbsp;&nbsp;<input style="opacity: 0;" type="checkbox" data-on-color="success" class="tmpTaskStatus" checked="" value="on"/></td><td class="listViewEntryValue taskType"></td><td><span class="pull-left taskName"></span></td></tr></tbody></table><?php if (empty($_smarty_tpl->tpl_vars['TASK_LIST']->value)) {?><table class="emptyRecordsDiv"><tbody><tr><td><?php echo vtranslate('LBL_NO_TASKS_ADDED',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</td></tr></tbody></table><?php }?></div></div><?php }
}
