<?php
/* Smarty version 4.3.2, created on 2026-03-11 14:09:59
  from '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/Settings.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_69b177b7de3c83_90088233',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2698a669585c2114eeede6183a9144f7da9b2e1d' => 
    array (
      0 => '/var/www/html/vtiger81/layouts/v7/modules/Settings/Whatsapp/Settings.tpl',
      1 => 1773150243,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b177b7de3c83_90088233 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="listViewPageDiv" id="listViewContainer" style="padding: 20px;">
    <div class="listViewTopMenuDiv" style="margin-bottom: 20px;">
        <div class="row-fluid">
            <div class="span6">
                <h4 style="margin-top: 10px;"><?php echo vtranslate('LBL_WHATSAPP_CHANNELS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h4>
            </div>
            <div class="span6">
                <div class="btn-group listViewActions pull-right">
                    <button class="btn btn-default" id="addButton" onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getCreateRecordUrl();?>
"'>
                        <i class="fa fa-plus"></i>&nbsp;<strong><?php echo vtranslate('LBL_ADD_CHANNEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="listViewContentDiv" id="listViewContents" style="margin-top: 20px;">
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    <th width="20%"><?php echo vtranslate('LBL_CHANNEL_NAME',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="30%"><?php echo vtranslate('LBL_APP_ID',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="20%"><?php echo vtranslate('LBL_PHONE_NUMBER_ID',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="15%"><?php echo vtranslate('LBL_STATUS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                    <th width="15%" style="text-align: center;"><?php echo vtranslate('LBL_ACTIONS',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</th>
                </tr>
            </thead>
            <tbody>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['RECORDS']->value, 'RECORD');
$_smarty_tpl->tpl_vars['RECORD']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['RECORD']->value) {
$_smarty_tpl->tpl_vars['RECORD']->do_else = false;
?>
                    <tr class="listViewEntries" data-id="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
">
                        <td><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getName();?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('app_id');?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('phone_number_id');?>
</td>
                        <td>
                            <?php if ($_smarty_tpl->tpl_vars['RECORD']->value->get('is_active')) {?>
                                <span class="label label-success"><?php echo vtranslate('LBL_ACTIVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span>
                            <?php } else { ?>
                                <span class="label label-important"><?php echo vtranslate('LBL_INACTIVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span>
                            <?php }?>
                        </td>
                        <td style="text-align: center;">
                            <div class="actions">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['RECORD']->value->getRecordLinks(), 'LINK');
$_smarty_tpl->tpl_vars['LINK']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['LINK']->value) {
$_smarty_tpl->tpl_vars['LINK']->do_else = false;
?>
                                    <a <?php if ($_smarty_tpl->tpl_vars['LINK']->value->get('linkurl')) {?> href='<?php echo $_smarty_tpl->tpl_vars['LINK']->value->get('linkurl');?>
' <?php }?> title="<?php echo vtranslate($_smarty_tpl->tpl_vars['LINK']->value->get('linklabel'),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
">
                                        <i class="<?php echo $_smarty_tpl->tpl_vars['LINK']->value->get('linkicon');?>
"></i>
                                    </a>&nbsp;&nbsp;
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </div>
                        </td>
                    </tr>
                <?php
}
if ($_smarty_tpl->tpl_vars['RECORD']->do_else) {
?>
                    <tr class="emptyRecordsDiv">
                        <td colspan="5" class="emptyRecordsMessage">
                            <?php echo vtranslate('LBL_NO_CHANNELS_FOUND',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>

                        </td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
    </div>
</div>
</div> <?php }
}
