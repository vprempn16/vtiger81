<?php
/* Smarty version 4.3.2, created on 2026-03-18 10:46:22
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/VTAtomCommentsMentions/LicenseManagerEdit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69ba827e458874_33426390',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cfceb6f2d78ed7614adfd29e6138ea095f6258c6' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/VTAtomCommentsMentions/LicenseManagerEdit.tpl',
      1 => 1773830737,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ba827e458874_33426390 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="editViewPageDiv editViewContainer" id="EditViewOutgoing" style="padding-top:0px;"><div class="col-lg-12 col-md-12 col-sm-12"><div><h3 style="margin-top: 0px;"><?php echo vtranslate('VTAtom Comments Mention License Manager',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3>&nbsp;</div><?php $_smarty_tpl->_assignInScope('WIDTHTYPE', $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('rowheight'));?><form id="LicenseManagerConfig" data-detail-url="<?php echo $_smarty_tpl->tpl_vars['LSITVIEWURL']->value;?>
" method="POST"><input type="hidden" name="default" value="false" /><input type="hidden" name="parent" value="Settings"/><input type="hidden" name="module" value="VTAtomCommentsMentions"/><input type="hidden" name="action" value="SaveLicense"/><div class="blockData"><br><div class="hide errorMessage"><div class="alert alert-danger"></div></div><div class="block"><div><h4><?php echo vtranslate('VTAtom Comments Mention License Manager',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4></div><hr><?php if ($_smarty_tpl->tpl_vars['LICENSE_KEY']->value != '') {?><div class="col-5 pull-right"><?php if ($_smarty_tpl->tpl_vars['IS_KEYACTIVE']->value == true) {?><button class="btn btn-default" type="button" value='deactivate' id="direct_api">Deactivate</button><?php } else { ?><button class="btn btn-default" type="button" value='activate' id="direct_api">Activate</button><?php }?></div><?php }?><table class="table editview-table no-border"><tbody><tr><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldLabel"><label><?php echo vtranslate('License Key',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label></td><td class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue" style="width:70%;" ><div class=" col-lg-6 col-md-6 col-sm-12"><input type="text" <?php if ($_smarty_tpl->tpl_vars['API_KEY']->value != '') {?> placeholder="<?php echo $_smarty_tpl->tpl_vars['APIKEY']->value;?>
" <?php }?> name="<?php echo $_smarty_tpl->tpl_vars['MODULENAME']->value;?>
" value="" class="inputElement" name="from_email_field" data-rule-email="true" data-rule-illegal="true" /><?php if ($_smarty_tpl->tpl_vars['IS_KEYVALID']->value == false && $_smarty_tpl->tpl_vars['LICENSE_KEY']->value != '') {?> <br><span style="color:red;"> Key is not valid</span> <?php }?></div></td></tr></tbody></table></div><br><?php if ($_smarty_tpl->tpl_vars['LICENSE_KEY']->value == '') {?><span style='color:red;'>License Key is empty.Please enter valid License key</span><?php }
if ($_smarty_tpl->tpl_vars['IS_KEYVALID']->value == false && $_smarty_tpl->tpl_vars['LICENSE_KEY']->value != '') {?><span style='color:red;'>License Key is not valid.Please enter valid License key</span><?php }
if (!$_smarty_tpl->tpl_vars['IS_KEYACTIVE']->value && $_smarty_tpl->tpl_vars['LICENSE_KEY']->value != '') {?><span style='color:red;'> Please activate the License key</span><?php }?><div class='modal-overlay-footer clearfix'><div class="row clearfix"><div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '><button type='submit' class='btn btn-success atm_saveButton' ><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>&nbsp;&nbsp;<a class='atm_cancelLink' data-dismiss="modal" href="#"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div></div></form></div></div>
<?php }
}
