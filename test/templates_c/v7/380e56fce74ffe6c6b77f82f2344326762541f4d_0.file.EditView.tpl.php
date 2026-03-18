<?php
/* Smarty version 4.3.2, created on 2026-03-17 07:50:44
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/EditView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b907d4a69e59_17909992',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '380e56fce74ffe6c6b77f82f2344326762541f4d' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/EditView.tpl',
      1 => 1773475457,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b907d4a69e59_17909992 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="editViewPageDiv">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <form class="form-horizontal" id="EditView" name="EditView" method="post" action="index.php">
            <input type="hidden" name="module" value="Whatsapp" />
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="action" value="ActionAjax" />
            <input type="hidden" name="mode" value="save" />
            <input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getId();?>
" />

            <div class="widget_header row-fluid">
                <div class="span12">
                    <h4><?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getId()) {
echo vtranslate('LBL_EDIT_CHANNEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);
} else {
echo vtranslate('LBL_ADD_CHANNEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);
}?></h4>
                </div>
            </div>

            <div class="contents">
                <table class="table table-bordered editViewContents">
                    <tbody>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_CHANNEL_NAME',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="name" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->getName();?>
" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_DESCRIPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</td>
                            <td class="fieldValue">
                                <textarea class="inputElement" name="description"><?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('description');?>
</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_APP_ID',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="app_id" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('app_id');?>
" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_APP_SECRET',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="password" class="inputElement" name="app_secret" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('app_secret');?>
" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_PHONE_NUMBER_ID',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="phone_number_id" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('phone_number_id');?>
" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_BUSINESS_ID',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <input type="text" class="inputElement" name="business_id" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('business_id');?>
" data-rule-required="true" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_ACCESS_TOKEN',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<span class="redColor">*</span></td>
                            <td class="fieldValue">
                                <textarea class="inputElement" name="access_token" data-rule-required="true"><?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('access_token');?>
</textarea>
                                <p class="help-block"><small><?php echo vtranslate('LBL_ACCESS_TOKEN_HELP',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</small></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle"><?php echo vtranslate('LBL_IS_ACTIVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</td>
                            <td class="fieldValue">
                                <input type="checkbox" name="is_active" <?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('is_active')) {?>checked<?php }?> value="1" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-success" type="submit"><strong><?php echo vtranslate('LBL_SAVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button>
                    <a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php }
}
