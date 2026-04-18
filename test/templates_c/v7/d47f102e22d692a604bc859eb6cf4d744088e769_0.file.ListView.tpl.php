<?php
/* Smarty version 4.3.2, created on 2026-04-17 11:15:08
  from '/var/www/html/vtiger81/layouts/v7/modules/BranchUsers/ListView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69e2163c2192e2_22917873',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd47f102e22d692a604bc859eb6cf4d744088e769' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/BranchUsers/ListView.tpl',
      1 => 1776424471,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e2163c2192e2_22917873 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="main-container clearfix"><div class="listViewPageDiv viewContent" id="listViewContent"><div class="col-sm-12 col-xs-12 content-area"><div class="module-action-bar clearfix"><div class="module-action-content clearfix row"><div class="col-lg-4 col-md-4 col-sm-4"><h3 class="module-title pull-left">&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;</h3><div><p class="current-filter-name pull-left"><span class="fa fa-chevron-right" aria-hidden="true"></span>&nbsp;<?php echo vtranslate('List','List');?>
&nbsp;</p></div></div><div class="col-lg-4 col-md-4 col-sm-4"><div id="messageBar" class="hide"></div></div><div class="col-lg-4 col-md-4 col-sm-4 text-right"><a class="btn addButton btn-default module-buttons" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit"><div class="fa fa-plus"></div>&nbsp;&nbsp;<?php echo vtranslate('LBL_ADD_RECORD','Vtiger');?>
</a></div></div></div><div class="col-sm-12 col-xs-12"><div id="listview-actions" class="listview-actions-container"><div class="row"><div class="col-md-6"><form method="get" action="index.php" class="form-inline"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" /><input type="hidden" name="view" value="List" /><input type="hidden" name="orderby" value="<?php echo $_smarty_tpl->tpl_vars['ORDERBY']->value;?>
" /><input type="hidden" name="sortorder" value="<?php echo $_smarty_tpl->tpl_vars['SORTORDER']->value;?>
" /><input type="hidden" name="pageLimit" value="<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
" /><div class="form-group"><input type="text" name="search_key" value="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="form-control" placeholder="<?php echo vtranslate('LBL_SEARCH','Vtiger');?>
" /></div><button type="submit" class="btn btn-default"><?php echo vtranslate('LBL_SEARCH','Vtiger');?>
</button></form></div><div class="col-md-3"></div><div class="col-md-3 text-right" style="line-height:34px;"><?php echo vtranslate('LBL_TOTAL','Vtiger');?>
: <?php echo $_smarty_tpl->tpl_vars['TOTAL_USERS']->value;?>
</div></div></div><div id="table-content" class="table-container"><table id="listview-table" class="table listview-table"><thead><tr class="listViewContentHeader"><th><?php echo vtranslate('LBL_ACTIONS','Vtiger');?>
</th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=user_name&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'user_name' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('User Name','Users');?>
</a></th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=first_name&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'first_name' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('First Name','Users');?>
</a></th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=last_name&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'last_name' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('Last Name','Users');?>
</a></th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=email1&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'email1' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('Email','Users');?>
</a></th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=role_name&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'role_name' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('Role','Users');?>
</a></th><th><?php echo vtranslate('Parent User','BranchUsers');?>
</th><th><a class="listViewContentHeaderValues" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&page=1&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&orderby=status&sortorder=<?php if ($_smarty_tpl->tpl_vars['ORDERBY']->value == 'status' && $_smarty_tpl->tpl_vars['SORTORDER']->value == 'ASC') {?>DESC<?php } else { ?>ASC<?php }?>"><?php echo vtranslate('Status','Users');?>
</a></th></tr></thead><tbody class="overflow-y"><?php if (empty($_smarty_tpl->tpl_vars['USERS']->value)) {?><tr class="emptyRecordsDiv"><td colspan="8"><div class="emptyRecordsContent"><?php echo vtranslate('LBL_NO_RECORDS_FOUND','Vtiger');?>
</div></td></tr><?php } else {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['USERS']->value, 'ROW', false, NULL, 'users', array (
));
$_smarty_tpl->tpl_vars['ROW']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ROW']->value) {
$_smarty_tpl->tpl_vars['ROW']->do_else = false;
?><tr class="listViewEntries" data-id="<?php echo $_smarty_tpl->tpl_vars['ROW']->value['id'];?>
"><td class="listViewRecordActions"><div class="table-actions"><span class="more dropdown action"><span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i title="<?php echo vtranslate('LBL_MORE_OPTIONS','Vtiger');?>
" class="fa fa-ellipsis-v icon"></i></span><ul class="dropdown-menu" style="top: auto; bottom: 45%;"><?php if ($_smarty_tpl->tpl_vars['IS_ADMIN']->value || $_smarty_tpl->tpl_vars['ROW']->value['id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value || $_smarty_tpl->tpl_vars['ROW']->value['parent_user_id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value || $_smarty_tpl->tpl_vars['ROW']->value['creator_id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value) {?><li><a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['ROW']->value['id'];?>
"><i class="fa fa-pencil"></i>&nbsp;<?php echo vtranslate('LBL_EDIT','Vtiger');?>
</a></li><?php }
if ($_smarty_tpl->tpl_vars['IS_ADMIN']->value) {?><li><a href="index.php?module=Users&view=DeleteAjax&record=<?php echo $_smarty_tpl->tpl_vars['ROW']->value['id'];?>
"><i class="fa fa-trash"></i>&nbsp;<?php echo vtranslate('LBL_DELETE','Vtiger');?>
</a></li><?php }?></ul></span></div></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php if ($_smarty_tpl->tpl_vars['IS_ADMIN']->value || $_smarty_tpl->tpl_vars['ROW']->value['id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value || $_smarty_tpl->tpl_vars['ROW']->value['parent_user_id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value || $_smarty_tpl->tpl_vars['ROW']->value['creator_id'] == $_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value) {?><a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Edit&record=<?php echo $_smarty_tpl->tpl_vars['ROW']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['user_name'];?>
</a><?php } else {
echo $_smarty_tpl->tpl_vars['ROW']->value['user_name'];
}?></span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['first_name'];?>
</span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['last_name'];?>
</span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['email1'];?>
</span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['role_name'];?>
</span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php if ($_smarty_tpl->tpl_vars['ROW']->value['parent_user_id'] > 0) {
echo getUserFullName($_smarty_tpl->tpl_vars['ROW']->value['parent_user_id']);
} else { ?>-<?php }?></span></span></td><td class="listViewEntryValue"><span class="fieldValue"><span class="value"><?php echo $_smarty_tpl->tpl_vars['ROW']->value['status'];?>
</span></span></td></tr><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?></tbody></table></div><div class="row" style="margin-top:10px;"><div class="col-md-12 text-right"><?php $_smarty_tpl->_assignInScope('PREV_PAGE', $_smarty_tpl->tpl_vars['PAGE']->value-1);
$_smarty_tpl->_assignInScope('NEXT_PAGE', $_smarty_tpl->tpl_vars['PAGE']->value+1);?><a class="btn btn-default btn-sm <?php if ($_smarty_tpl->tpl_vars['PAGE']->value <= 1) {?>disabled<?php }?>" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&orderby=<?php echo $_smarty_tpl->tpl_vars['ORDERBY']->value;?>
&sortorder=<?php echo $_smarty_tpl->tpl_vars['SORTORDER']->value;?>
&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&page=<?php if ($_smarty_tpl->tpl_vars['PAGE']->value <= 1) {?>1<?php } else {
echo $_smarty_tpl->tpl_vars['PREV_PAGE']->value;
}?>"><i class="fa fa-chevron-left"></i></a><span style="margin:0 8px;"><?php echo $_smarty_tpl->tpl_vars['PAGE']->value;?>
 / <?php echo $_smarty_tpl->tpl_vars['TOTAL_PAGES']->value;?>
</span><a class="btn btn-default btn-sm <?php if ($_smarty_tpl->tpl_vars['PAGE']->value >= $_smarty_tpl->tpl_vars['TOTAL_PAGES']->value) {?>disabled<?php }?>" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=List&search_key=<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['SEARCH_KEY']->value);?>
&orderby=<?php echo $_smarty_tpl->tpl_vars['ORDERBY']->value;?>
&sortorder=<?php echo $_smarty_tpl->tpl_vars['SORTORDER']->value;?>
&pageLimit=<?php echo $_smarty_tpl->tpl_vars['PAGELIMIT']->value;?>
&page=<?php if ($_smarty_tpl->tpl_vars['PAGE']->value >= $_smarty_tpl->tpl_vars['TOTAL_PAGES']->value) {
echo $_smarty_tpl->tpl_vars['TOTAL_PAGES']->value;
} else {
echo $_smarty_tpl->tpl_vars['NEXT_PAGE']->value;
}?>"><i class="fa fa-chevron-right"></i></a></div></div></div></div></div></div>

<?php }
}
