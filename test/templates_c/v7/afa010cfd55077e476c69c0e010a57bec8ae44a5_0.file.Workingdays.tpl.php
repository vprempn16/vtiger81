<?php
/* Smarty version 4.3.2, created on 2025-11-27 13:27:49
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/ServiceCompetency/Workingdays.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_692851d55f9887_89753867',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'afa010cfd55077e476c69c0e010a57bec8ae44a5' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/ServiceCompetency/Workingdays.tpl',
      1 => 1764250057,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_692851d55f9887_89753867 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;"><div class="col-lg-12 col-md-12 col-sm-12"><div></div><form id="Workingdays" data-detail-url="<?php echo $_smarty_tpl->tpl_vars['LSITVIEWURL']->value;?>
" method="POST"><input type="hidden" name="default" value="false" /><input type="hidden" name="parent" value="Settings"/><input type="hidden" name="module" value="ServiceCompetency"/><input type="hidden" name="action" value="SaveAjax"/><input type="hidden" name="mode" value="working_days"/><div class="blockData"><br><div class="hide errorMessage"><div class="alert alert-danger"></div></div><div class="block"><div><h4><?php echo vtranslate('User Working Days',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><table class="table editview-table no-border"><tbody><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['RECORDS']->value, 'NAME', false, 'ID');
$_smarty_tpl->tpl_vars['NAME']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ID']->value => $_smarty_tpl->tpl_vars['NAME']->value) {
$_smarty_tpl->tpl_vars['NAME']->do_else = false;
?><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel"><input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" name="userid"><label><?php echo vtranslate($_smarty_tpl->tpl_vars['NAME']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue" style="width:70%;" ><div class=" col-lg-3 col-md-3 col-sm-6"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['WORKING_DAYS']->value[$_smarty_tpl->tpl_vars['ID']->value]['working_days'] != '') {?> value="<?php echo $_smarty_tpl->tpl_vars['WORKING_DAYS']->value[$_smarty_tpl->tpl_vars['ID']->value]['working_days'];?>
" <?php }?> class="inputElement working_days" name="from_email_field" data-rule-email="true" data-rule-illegal="true" /></div><div class=" col-lg-3 col-md-3 col-sm-6"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['WORKING_DAYS']->value[$_smarty_tpl->tpl_vars['ID']->value]['working_hours'] != '') {?> value="<?php echo $_smarty_tpl->tpl_vars['WORKING_DAYS']->value[$_smarty_tpl->tpl_vars['ID']->value]['working_hours'];?>
 " <?php }?> class="inputElement working_days" name="from_email_field" data-rule-email="true" data-rule-illegal="true" /></div></td></tr><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></tbody></table></div><br><div class='modal-overlay-footer clearfix'><div class="row clearfix"><div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '><button type='button' class='btn btn-success wd_saveButton' ><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>&nbsp;&nbsp;<a class='atm_cancelLink' data-dismiss="modal" href="#"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div></div></form></div></div> <!--editViewPageDiv end -->
<?php }
}
