<?php
/* Smarty version 4.3.2, created on 2025-11-25 13:42:25
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/ServiceCompetency/Edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_6925b24142a0e2_16675599',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61d764428742ff57e37382b90c4d56372129ad5c' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/ServiceCompetency/Edit.tpl',
      1 => 1764078134,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6925b24142a0e2_16675599 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container-fluid main-scroll paddingTop15" id="layoutEditorContainer"><div><h3 style="margin-top: 0px;"><?php echo vtranslate('Service Competency Settings',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3>&nbsp;</div><div class="contents tabbable ui-sortable"><ul class="nav nav-tabs layoutTabs massEditTabs marginBottom10px"><li class="rolePriceTab active"><a data-toggle="tab" href="#rolePriceLayout" data-url="" data-mode="showRolePricing" aria-expanded="false"><strong>Role Pricing</strong></a></li><li class="workingDaysTab"><a data-toggle="tab" href="#userWorkingdays" data-url="" data-mode="showWorkingdays" aria-expanded="false"><strong>User Working Days</strong></a></li></ul><div class="tab-content layoutContent themeTableColor overflowVisible"><div id="rolePriceLayout" class="tab-pane active"><div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;"><div class="col-lg-12 col-md-12 col-sm-12"><div></div><?php $_smarty_tpl->_assignInScope('WIDTHTYPE', $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'));?><form id="RolePricing" data-detail-url="<?php echo $_smarty_tpl->tpl_vars['LSITVIEWURL']->value;?>
" method="POST"><input type="hidden" name="default" value="false" /><input type="hidden" name="parent" value="Settings"/><input type="hidden" name="module" value="ServiceCompetency"/><input type="hidden" name="action" value="SaveAjax"/><div class="blockData"><br><div class="hide errorMessage"><div class="alert alert-danger"></div></div><div class="block"><div><h4><?php echo vtranslate('Role Pricing',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><table class="table editview-table no-border"><tbody><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ROLES']->value, 'ROLE', false, 'VAL');
$_smarty_tpl->tpl_vars['ROLE']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['VAL']->value => $_smarty_tpl->tpl_vars['ROLE']->value) {
$_smarty_tpl->tpl_vars['ROLE']->do_else = false;
?><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel"><label><?php echo vtranslate($_smarty_tpl->tpl_vars['VAL']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue" style="width:70%;" ><div class=" col-lg-6 col-md-6 col-sm-12"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['ROLE']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['RECORDS']->value[$_smarty_tpl->tpl_vars['ROLE']->value] != '') {?> value="<?php echo $_smarty_tpl->tpl_vars['RECORDS']->value[$_smarty_tpl->tpl_vars['ROLE']->value];?>
" <?php }?> class="inputElement roleprice" name="from_email_field" data-rule-email="true" data-rule-illegal="true" /></div></td></tr><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></tbody></table></div><br><div class='modal-overlay-footer clearfix'><div class="row clearfix"><div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '><button type='submit' class='btn btn-success atm_saveButton' ><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>&nbsp;&nbsp;<a class='atm_cancelLink' data-dismiss="modal" href="#"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div></div></form></div></div> <!--editViewPageDiv end --></div><!--rolePriceLayout end --><div id="userWorkingdays" class="tab-pane"></div></div></div></div>

<?php }
}
